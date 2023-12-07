<?php
namespace App\Service;

use App\Entity\Answer;
use App\Entity\Arc;
use App\Entity\Question;
use App\Entity\QuestionMetadata;
use App\Entity\User;
use App\Helpers\DateTimeHelperTrait;
use App\Repository\ArcRepository;
use App\Repository\QuestionRepository;
use App\Service\Breadcrumb\Breadcrumb;
use App\Service\Breadcrumb\BreadcrumbItem;
use App\Service\Import\ImportCsvService;
use App\Service\Uploader\Uploader;
use App\Utils\ServiceTrait;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class ArcService {
    use ServiceTrait;
    use DateTimeHelperTrait;

    private Slugify $slugify;
    private ?string $tmpFile;

    public function __construct(
        private ImportCsvService $importCsvService,
        private QuestionRepository $questionRepository,
        private ArcRepository $arcRepository,
        private LoggerInterface $logger,
        private Security $security,
        private EntityManagerInterface $manager,
        private PaginatorInterface $paginator,
        private Uploader $uploader,
        private ParameterBagInterface $parameters,
    ) {
        $this->slugify = new Slugify;
        $this->tmpFile = null;
    }

    /**
     * index
     *
     * @param  Request $request
     * @return array
     */
    public function index(Request $request): array {
        $breadcrumb = new Breadcrumb([
            new BreadcrumbItem('Liste des utilisateurs'),
        ]);

        $paginatedArcs = $this->getArcs($request);

        return compact('paginatedArcs', 'breadcrumb');
    }

    /**
     * @param  Request $request
     * @return PaginationInterface
     */
    public function getArcs(Request $request): PaginationInterface {

        $data = $this->arcRepository->findAll();
        $page = $request->query->getInt('page', 1);
        $nbItems = $request->query->getInt('nbItems', 10);

        return $this->paginator->paginate(
            $data,
            /* query NOT result */
            $page,
            /*page number*/
            $nbItems, /*limit per page*/
        );
    }

    /**
     * import
     *
     * @param  Form $form
     * @return array
     */
    public function import(Form $form): bool {
        $arcName = $form->get('name')->getData();
        $file = $form->get('file')->getData();
        $position = $form->get('position')->getData() ?? $this->getPosition();

        $data = $this->importCsvService->getDataFromCsv($file);
        $arc = $this->arcRepository->findOneBy(['name' => $arcName]);
        if($arc === null) {
            $arc = new Arc;
            $arc->setName($arcName)
                ->setPosition($position)
            ;
        }
        $this->handlePosition($arc, $position);

        if($data && count($data) > 0) {
            $questionPosition = $this->questionRepository->findLastQuestionByArc($arc)?->getPosition() ?? 0;

            foreach($data as $quote) {
                $questionExist = $this->questionRepository->findOneBy(['name' => $quote->citation, 'arc' => $arc]);
                if($questionExist === null) {
                    $question = new Question;
                    $questionPosition++;

                    $question->setName($quote->citation)
                        ->setPosition($questionPosition)
                        ->setSlug($this->slugify->slugify($question->getName()))
                        ->setCreatedAt($this->now())
                    ;

                    if(property_exists($quote, 'reponse')) {
                        $answer = new Answer;

                        $answer->setName($quote->reponse)
                            ->setCreatedAt($this->now())
                            ->setIsAnswer(true)
                        ;
                        $question->addAnswer($answer)
                            ->setChoices(false)
                        ;
                    }

                    if(property_exists($quote, 'chapitre')) {
                        $metadata = new QuestionMetadata;
                        $metadata->setName('chapitre')
                            ->setValue($quote->chapitre)
                        ;
                        $question->addMetadata($metadata);
                    }

                    if(property_exists($quote, 'page')) {
                        $metadata = new QuestionMetadata;
                        $metadata->setName('page')
                            ->setValue($quote->page)
                        ;
                        $question->addMetadata($metadata);
                    }

                    if(property_exists($quote, 'coeur')) {
                        $metadata = new QuestionMetadata;
                        $metadata->setName('coeur')
                            ->setValue($quote->coeur)
                        ;
                        $question->addMetadata($metadata);
                    }
                    $arc->addQuestion($question);
                }
            }
        }

        $this->uploader->upload($file, [
            'targetDir' => $this->parameters->get('arc_directory'),
            'fileType' => 'text',
            'renamed' => true
        ]);

        return $arc->getCreatedAt() === null ? $this->create($form, $arc) : $this->update($arc);
    }

    /**
     * edit
     *
     * @param  Form $form
     * @param  Arc $arc
     * @return Uploader|bool
     */
    public function edit(Form $form, Arc $arc): Uploader|bool {
        $upload = $this->saveImage($form, $arc);
        $this->handlePosition($arc, $form->get('position')->getData());

        if($upload instanceof Uploader) {
            return $upload;
        }

        return $this->update($arc);
    }

    /**
     * saveImage
     *
     * @param  Form $form
     * @param  Arc $arc
     * @return Arc|Uploader
     */
    public function saveImage(Form $form, Arc $arc): Arc|Uploader {
        $image = $form->get('image')->getData();
        if($image instanceof UploadedFile) {
            $upload = $this->uploader->upload($image, [
                'targetDir' => $this->parameters->get('arc_directory'),
                'fileType' => 'image'
            ]);
            if($arc->getImage() !== null) {
                $this->tmpFile = $arc->getImage();
            }
            $arc->setImage($this->parameters->get('uploads_directory').$upload->getUploadPath());
            if($upload->hasErrors()) {
                $message = '';
                foreach($upload->getErrors() as $error) {
                    $message .= $error->getMessage().', ';
                }
                $this->addFlash('danger', $message);
                return $upload;
            }
        }
        return $arc;
    }

    /**
     * getPosition
     *
     * @return int
     */
    public function getPosition(): int {
        $last = $this->arcRepository->findLastPosition();

        if($last instanceof Arc) {
            return $last->getPosition() + 1;
        }

        return 1;
    }

    /**
     * handlePosition
     *
     * @param  Arc $arc
     * @param  int|null $position
     * @return void
     */
    public function handlePosition(Arc $arc, ?int $position = null): void {
        if($position === null) {
            $position = $this->getPosition();
        }

        $arcsToUpdate = $this->arcRepository->findArcAfter($position, $arc);
        $arc->setPosition($position);

        if(count($arcsToUpdate) > 0) {
            $pos = $position + 1;
            foreach($arcsToUpdate as $value) {
                if($arc->getId() !== $value->getId()) {
                    $value->setPosition($pos);
                    $this->manager->persist($value);
                    $pos++;
                }
            }

            $this->manager->flush();
        }
    }

    /**
     * create
     *
     * @param  Arc $arc
     * @param  Form $form
     * @return bool
     */
    public function create(Form $form, Arc $arc): bool {
        $arc
            ->setCreatedAt($this->now())
            ->setSlug($arc->getName());
        $this->handlePosition($arc, $form->get('position')->getData());

        $result = $this->save($arc);
        $user = $this->getUser();

        if($result) {
            $this->addFlash('success', 'Arc crée 🚀');
            $this->logger->info("Arc `{arc}` created by #{admin}", [
                "arc" => $user->getUsername(),
                'admin' => $this->getUser()->getId()
            ]);
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement de cet arc !');
        }

        return $result;
    }

    /**
     * update
     *
     * @param  Arc $arc
     * @return bool
     */
    public function update(Arc $arc): bool {
        $arc->setUpdatedAt($this->now());

        $this->handlePosition($arc, $arc->getPosition());

        if($this->tmpFile !== null) {
            $this->uploader->remove($this->tmpFile);
        }
        $result = $this->save($arc);

        if($result) {
            $this->addFlash('success', 'Arc enregistré 🚀');
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement de cet arc !');
        }

        return $result;
    }

    /**
     * save
     *
     * @param  Arc $arc
     * @return bool
     */
    public function save(Arc $arc): bool {
        try {
            $this->manager->persist($arc);
            $this->manager->flush();
            $this->logger->info("Arc `{arc}` has been saved in DB by #{admin}", [
                "arc" => $arc->getName(),
                'admin' => $this->getUser()->getId()
            ]);
            return true;
        } catch (ORMException $e) {
            $this->logger->error($e->getMessage());
            $this->addFlash('danger', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * get logged User
     *
     * @return User
     */
    private function getUser(): ?User {
        $user = $this->security->getUser();

        if($user instanceof User) {
            return $user;
        }
        return null;
    }

    public function show(Arc $arc, Request $request): array {
        $data = $arc->getQuestions();
        $page = $request->query->getInt('page', 1);
        $nbItems = $request->query->getInt('nbItems', 10);

        $paginatedQuestions = $this->paginator->paginate(
            $data,
            /* query NOT result */
            $page,
            /*page number*/
            $nbItems, /*limit per page*/
        );
        
        return compact('arc', 'paginatedQuestions');
    }

    /**
     * remove
     *
     * @param  Arc $object
     * @return object|bool
     */
    public function remove(Arc $arc): bool|object {
        try {
            $this->uploader->remove($arc->getImage());

            $this->manager->remove($arc);
            $this->manager->flush();
            $this->logger->info('Arc {arcname} is removed form db', ['arcname' => $arc->getName()]);
            return $this->sendNoContent();
        } catch (ORMException $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la suppression de cet arc !');
            $this->logger->error($e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->logger->error($e->getMessage());
            return false;
        }
    }

}
