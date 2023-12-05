<?php
namespace App\Service\Breadcrumb;

use Symfony\Component\HttpFoundation\Request;
use App\Service\Breadcrumb\BreadcrumbItem;

class BreadcrumbGenerator {

    private ?Request $request = null;

    public function __construct(
        private ?Breadcrumb $breadcrumb = null
    ) {
        $this->request = Request::createFromGlobals();
    }

    /**
     * breadcrumbStart
     *
     * @return string
     */
    private function breadcrumbStart(): string {

        return '<nav style="--bs-breadcrumb-divider: \''.$this->breadcrumb->getSeparator().'\';" aria-label="breadcrumb">
            <ol class="breadcrumb">';
    }

    /**
     * breadcrumbEnd
     *
     * @return string
     */
    private function breadcrumbEnd(): string {
        return '</ol>
            </nav>';
    }

    /**
     * breadcrumbItem
     *
     * @param  mixed $item
     * @param  mixed $isActive
     * @return string
     */
    private function breadcrumbItem(BreadcrumbItem $item, bool $isActive = false): string {
        $active = $isActive ? ' active" aria-current="page' : '';
        $html = '<li class="breadcrumb-item'.$active.'">';
        $html .= $isActive || is_null($item->getLink()) ? $item->getName() : '<a href="'.$item->getLink().'">'.$item->getName().'</a>';

        return $html.'</li>';
    }

    /**
     * lastKey
     *
     * @param  mixed $values
     * @return int
     */
    private function lastKey(array $values = []): int {
        $count = count($values);
        if($count === 0 || $count === 1) {
            return $count;
        }
        return ($count - 1);
    }

    /**
     * generate
     *
     * @return string
     */
    public function generate(): ?string {
        $path = $this->request->getPathInfo();
        $html = $this->breadcrumbStart();

        if($this->breadcrumb->getHomePage()) {
            if(str_starts_with($path, '/admin')) {
                $route = '/admin';
            } else {
                $route = '/';
            }

            $this->breadcrumb->addItem(new BreadcrumbItem('Accueil', $route));
        }
        $lastKey = $this->lastKey($this->breadcrumb->getItems());

        foreach($this->breadcrumb->getItems() as $key => $item) {
            $html .= $this->breadcrumbItem($item, $key === $lastKey);
        }
        $html .= $this->breadcrumbEnd();

        return $html;
    }
}