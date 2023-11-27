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
use App\Service\Import\ImportCsvService;
use App\Utils\ServiceTrait;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

final class ArcService {
    use ServiceTrait;
    use DateTimeHelperTrait;

    private Slugify $slugify;

    public function __construct(
        private ImportCsvService $importCsvService,
        private QuestionRepository $questionRepository,
        private ArcRepository $arcRepository,
        private LoggerInterface $logger,
        private Security $security,
        private EntityManagerInterface $manager
    ) {
        $this->slugify = new Slugify;
    }

    public function index(Request $request): array {

        return [];
    }

    /**
     * import
     *
     * @param  mixed $form
     * @return array
     */
    public function import(Form $form): bool {
        $arcName = $form->get('name')->getData();
        $file = $form->get('file')->getData();
        $position = $form->get('position')->getData() ?? ($this->getPosition() + 1);
        $data = $this->importCsvService->getDataFromCsv($file);
        $arc = $this->arcRepository->findOneBy(['name' => $arcName]);
        if($arc === null) {
            $arc = new Arc;
            $arc->setName($arcName)
                ->setPosition($position)
            ;
        }

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

        return $arc->getCreatedAt() === null ? $this->create($arc) : $this->update($arc);
    }

    public function getPosition(): int {
        $last = $this->arcRepository->findLastPosition();

        if($last instanceof Arc) {
            return $last->getPosition();
        }
        return 0;
    }

    public function updatePosition(int $position): void {

    }

    /**
     * create
     *
     * @param  mixed $user
     * @return bool
     */
    public function create(Arc $arc): bool {
        $arc
            ->setCreatedAt($this->now())
            ->setSlug($arc->getName());

        $result = $this->save($arc);
        $user = $this->getUser();

        if($result) {
            $this->addFlash('success', 'Arc crée 🚀');
            $this->logger->info("Arc `{arc}` created by #{admin}", [
                "arc" => $user->getUsername(),
                'admin' => $this->getUser()->getId()
            ]);
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement de ce compte !');
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
        $result = $this->save($arc);

        if($result) {
            $this->addFlash('success', 'Arc enregistré 🚀');
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement de ce compte !');
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

}
