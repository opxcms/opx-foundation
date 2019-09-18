<?php

namespace Core\Foundation\Templater\Traits;

use Core\Foundation\Auth\Contracts\UserContract;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Authorization\AdminAuthorization;

trait TemplaterAuthorization
{
    /** @var  array */
    protected $ignorePermissions;

    /** @var  array */
    protected $skipPermissions;

    /** @var  boolean */
    protected $ignoreAllPermissions;

    /** @var  boolean */
    protected $skipAllPermissions;

    /** @var  UserContract */
    protected $user;

    /**
     * Set user to check permissions in template.
     *
     * @param UserContract $user
     *
     * @return  $this
     */
    public function forUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Ignore given permissions. Equal to user has them.
     *
     * @param string|array $permissions
     *
     * @return  $this
     */
    public function ignorePermissions($permissions): self
    {
        if (!$this->ignorePermissions) {
            $this->ignorePermissions = [];
        }

        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        if ($this->skipPermissions) {
            foreach ($this->skipPermissions as $key => $skippedPermission) {
                if (isset($permissions[$skippedPermission])) {
                    unset($this->skipPermissions[$key]);
                }
            }
        }

        $this->ignorePermissions = array_merge($this->ignorePermissions, $permissions);

        return $this;
    }

    /**
     * Skip permissions. Like user has no them.
     *
     * @param string|array $permissions
     *
     * @return  $this
     */
    public function skipPermissions($permissions): self
    {
        if (!$this->skipPermissions) {
            $this->skipPermissions = [];
        }

        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        if ($this->ignorePermissions) {
            foreach ($this->ignorePermissions as $key => $ignoredPermission) {
                if (isset($permissions[$ignoredPermission])) {
                    unset($this->ignorePermissions[$key]);
                }
            }
        }

        $this->skipPermissions = array_merge($this->skipPermissions, $permissions);

        return $this;
    }

    /**
     * Ignore all permissions like user has all.
     *
     * @return  $this
     */
    public function ignoreAllPermissions(): self
    {
        $this->ignoreAllPermissions = true;
        $this->skipAllPermissions = false;

        return $this;
    }

    /**
     * Skip all permissions like user has no any.
     *
     * @return  $this
     */
    public function skipAllPermissions(): self
    {
        $this->skipAllPermissions = true;
        $this->ignoreAllPermissions = false;

        return $this;
    }

    /**
     * Resolve permissions for all subjects.
     *
     * @return  $this
     */
    public function resolvePermissions(): self
    {
        $template = [];

        if (isset($this->template['sections'])) {
            foreach ($this->template['sections'] as $section) {
                $resolved = $this->resolvePermission($section);
                if ($resolved) {
                    $template['sections'][] = $resolved;
                }
            }
        }

        if (isset($this->template['groups'])) {
            foreach ($this->template['groups'] as $group) {
                $resolved = $this->resolvePermission($group);
                if ($resolved) {
                    $template['groups'][] = $resolved;
                }
            }
        }

        if (isset($this->template['fields'])) {
            foreach ($this->template['fields'] as $field) {
                $resolved = $this->resolvePermission($field);
                if ($resolved) {
                    $template['fields'][] = $resolved;
                }
            }
        }

        $this->template = $template;

        return $this;
    }

    /**
     * Resolve permissions for given subject.
     *
     * @param array|null $subject
     *
     * @return  array|null
     */
    protected function resolvePermission($subject): ?array
    {
        if (empty($subject)) {
            return null;
        }

        if (!isset($subject['permissions'])) {
            $subject['can_view'] = true;
            $subject['can_edit'] = true;

            return $subject;
        }

        $canEdit = empty($subject['permissions']['edit']) || $this->checkPermission($subject['permissions']['edit']);
        $canView = $canEdit || empty($subject['permissions']['view']) || $this->checkPermission($subject['permissions']['view']);

        if (!$canView) {
            return null;
        }

        $subject['can_view'] = $canView;
        $subject['can_edit'] = $canEdit;
        unset($subject['permissions']);

        return $subject;
    }

    /**
     * Check permission.
     *
     * @param string|null $permission
     *
     * @return  bool
     */
    protected function checkPermission($permission): bool
    {
        if ($permission === null || $permission === '') {
            return true;
        }

        if ($permission === 'none') {
            return false;
        }

        if ($this->skipAllPermissions || isset($this->skipPermissions[$permission])) {
            return false;
        }

        if ($this->ignoreAllPermissions || isset($this->ignorePermissions[$permission])) {
            return true;
        }

        return AdminAuthorization::can($permission);
//        return $this->user()->can($permission);
    }

    /**
     * Get user to check permissions in template.
     *
     * @return  UserContract
     */
    protected function user(): UserContract
    {
        if (!$this->user) {
            $this->user = Auth::user();
        }

        return $this->user;
    }
}