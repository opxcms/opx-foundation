<?php

namespace Core\Http\Controllers;

use Core\Foundation\Templater\Templater;
use Core\Traits\NotAuthorizedResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use JsonException;

class APIFormController extends BaseController
{
    use NotAuthorizedResponse;

    /** @var string Component to load */
    public $component = 'opx-form';

    /** @var null|string Url to controller */
    public $base;

    /**
     * Returns list component with associated settings.
     *
     * @param int|null $id
     * @param Templater $template
     * @param string $caption
     * @param string|null $save
     * @param string|null $redirect
     * @param bool $hints
     *
     * @return  JsonResponse
     */
    public function responseFormComponent(?int $id, Templater $template, string $caption, $save = null, $redirect = null, $hints = false): JsonResponse
    {
        $data = $template->get();

        $response = [
            'component' => $this->component,
            'data' => [
                'id' => $id,
                'caption' => $caption,
                'controller_base' => $this->base ?? null,
                'save' => $save,
                'form' => $data,
                'hints' => $hints,
            ],
        ];

        if ($redirect) {
            $response['redirect'] = $redirect;
        }

        return response()->json($response);
    }

    /**
     * Return validator fail response with errors.
     *
     * @param array $errors
     *
     * @return  JsonResponse
     */
    public function responseValidationError(array $errors): JsonResponse
    {
        return response()->json(['message' => 'messages.validation_error', 'errors' => $errors], 420);
    }

    /**
     * Get attributes from model if it present in values.
     *
     * @param Model|null $model
     * @param string|array $attributes
     *
     * @return  array
     */
    protected function getAttributes(?Model $model, $attributes): array
    {
        if ($model === null) {
            return [];
        }

        if (is_string($attributes)) {
            $attributes = [$attributes];
        }

        $attrs = [];
        $modelAttrs = $model->getAttributes();

        foreach ($attributes as $srcAttr => $destAttr) {
            if (!is_string($srcAttr)) {
                $srcAttr = $destAttr;
            }
            if (array_key_exists($srcAttr, $modelAttrs)) {
                $attrs[$destAttr] = $modelAttrs[$srcAttr];
            }
        }

        return $attrs;
    }

    /**
     * Set attributes to model if it present in values.
     *
     * @param Model $model
     * @param $values
     * @param string|array $attributes
     *
     * @return  Model
     */
    protected function setAttributes(Model $model, $values, $attributes): Model
    {
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }

        foreach ($attributes as $srcAttr => $destAttr) {
            if (!is_string($srcAttr)) {
                $srcAttr = $destAttr;
            }
            if (array_key_exists($srcAttr, $values)) {
                $model->setAttribute($destAttr, $values[$srcAttr]);
            }
        }

        return $model;
    }

    /**
     * Store image from request.
     *
     * @param Request $request
     * @param string $templatePath
     *
     * @return  JsonResponse
     *
     * @deprecated
     */
    protected function storeImageFromRequest(Request $request, string $templatePath): JsonResponse
    {
        return $this->storeFilesFromRequest($request, $templatePath);
    }

    /**
     * Store files from request.
     *
     * @param Request $request
     * @param string $templatePath
     *
     * @return  JsonResponse
     * @throws JsonException
     */
    protected function storeFilesFromRequest(Request $request, string $templatePath): JsonResponse
    {
        $template = new Templater($templatePath);

        $name = $request->input('name');
        $data = $request->input('data');

        // Just need to set field with image to template. Image will be stored automaticly according template settings.
        $template->fillValuesFromArray([$name => [$data]]);
        $res = json_decode($template->getEditableValues()[$name], true, 512, JSON_THROW_ON_ERROR)[0];

        return response()->json($res);
    }
}
