<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataModuleBasic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('amod_module_basic')->insert(
            [
                ['project_name' => 'public_project', 'module_name' => 'product_combine', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'public_project', 'module_name' => 'product_combine2', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'public_project', 'module_name' => 'recommend_product', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'amodvis_company', 'module_name' => 'Banner', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'amodvis_company', 'module_name' => 'Data', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'amodvis_company', 'module_name' => 'Feature', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'amodvis_company', 'module_name' => 'Footer', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'amodvis_company', 'module_name' => 'Header', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'amodvis_company', 'module_name' => 'Introduction', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'amodvis_company', 'module_name' => 'Resource', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'home_company', 'module_name' => 'AblityItems', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'home_company', 'module_name' => 'CardItems', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'home_company', 'module_name' => 'IntroBanner', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'home_company', 'module_name' => 'IntroTab', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
                ['project_name' => 'home_company', 'module_name' => 'SlideBanner', 'nick_name' => '', 'des' => '', 'thumbnail' => '/laravle-amodvis/public_project/public/images/thumb/comming.gif'],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
