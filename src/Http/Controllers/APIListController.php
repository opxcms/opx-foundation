<?php

namespace Core\Http\Controllers;

use Core\Traits\NotAuthorizedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class APIListController extends BaseController
{
    use NotAuthorizedResponse;

    protected $component = 'opx-list';
    protected $caption;
    protected $description;
    protected $source;

    /** @var array */
    protected $search;
    /** @var array */
    protected $filters;
    /** @var array */
    protected $order;

    /** @var string */
    protected $delete;
    /** @var string */
    protected $restore;
    /** @var string */
    protected $enable;
    /** @var string */
    protected $disable;
    /** @var string */
    protected $edit;
    /** @var string */
    protected $add;

    protected $children;

    protected $icons;

    /**
     * Returns list component with associated settings.
     *
     * @return  JsonResponse
     */
    protected function responseListComponent(): JsonResponse
    {
        $response = [
            'component' => $this->component,
            'data' => [
                'caption' => $this->caption,
                'description' => $this->description,
                'source' => $this->source,

                'search' => $this->getSearch(),
                'filters' => $this->getFilters(),
                'order' => $this->getOrder(),

                'enable' => $this->getEnableLink(),
                'disable' => $this->getDisableLink(),
                'delete' => $this->getDeleteLink(),
                'restore' => $this->getRestoreLink(),
                'add' => $this->getAddLink(),
                'edit' => $this->getEditLink(),

                'children' => $this->children,

                'icons' => $this->icons,
            ],
        ];

        if (method_exists($this, 'makeQuickNav')) {
            $response['data']['quick_nav'] = $this->makeQuickNav();
        }

        return response()->json($response);
    }

    /**
     * Returns list component with associated settings.
     *
     * @return  JsonResponse
     */
    public function getIndex(): JsonResponse
    {
        return $this->responseListComponent();
    }

    /**
     * Get add link.
     *
     * @return  string
     */
    protected function getAddLink(): ?string
    {
        return $this->add;
    }

    /**
     * Get edit link.
     *
     * @return  string
     */
    protected function getEditLink(): ?string
    {
        return $this->edit;
    }

    /**
     * Get edit link.
     *
     * @return  string
     */
    protected function getEnableLink(): ?string
    {
        return $this->enable;
    }

    /**
     * Get edit link.
     *
     * @return  string
     */
    protected function getDisableLink(): ?string
    {
        return $this->disable;
    }

    /**
     * Get edit link.
     *
     * @return  string
     */
    protected function getDeleteLink(): ?string
    {
        return $this->delete;
    }

    /**
     * Get edit link.
     *
     * @return  string
     */
    protected function getRestoreLink(): ?string
    {
        return $this->restore;
    }

    /**
     * Get filters.
     *
     * @return  array|null
     */
    protected function getFilters(): ?array
    {
        return $this->filters;
    }

    /**
     * Get order options.
     *
     * @return  array|null
     */
    protected function getOrder(): ?array
    {
        return $this->order;
    }

    /**
     * Get search options.
     *
     * @return  array|null
     */
    protected function getSearch(): ?array
    {
        return $this->search;
    }

    /**
     * Make record for opx-list-item.
     *
     * @param $id
     * @param $title
     * @param $subtitle
     * @param $description
     * @param $properties
     * @param $enabled
     * @param $isDeleted
     * @param int $childrenCount
     *
     * @return  array
     */
    protected function makeListRecord($id, $title, $subtitle, $description, $properties, $enabled, $isDeleted, $childrenCount = 0): array
    {
        return [
            'id' => $id,
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $description,
            'properties' => $properties,
            'enabled' => $enabled,
            'deleted' => $isDeleted,
            'children_count' => $childrenCount,
        ];
    }
}
