<?php

namespace Core\Http\Controllers\Manage;

use Core\Facades\Site;
use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagePanelController extends Controller
{
    /** @var  array  Scripts for manage panel */
    protected $scripts = [
//        0 => ['manage/assets/system/js/vendor.js'],
//        1 => ['manage/assets/system/js/components.js'],
        10 => ['manage/assets/system/js/opx.js'],
    ];

    /** @var  array  Styles for manage panel */
    protected $styles = [
        0 => ['manage/assets/system/css/mades.css'],
        1 => ['manage/assets/system/css/admin-layout.css'],
        2 => [],
    ];

    public function loadManagePanel(Request $request)
    {
        $this->collectAssetsFromModules();
        Site::setAssetScripts($this->scripts);
        Site::setAssetStyles($this->styles);

        return view('manage.loader');
    }

    /**
     * Collect assets from registered modules.
     *
     * @return  void
     */
    protected function collectAssetsFromModules()
    {
        // TODO Collect assets set in modules
    }
}
