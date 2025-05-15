<?php

namespace App\Console\Commands;

use App\Helpers\TreeFilesHelper;
use Illuminate\Console\Command;

class SearchIndexGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searchIndex:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate index file for local search engine';

    private array $excludeFiles = [
        'blankLayout.blade.php',
        'horizontalMenu.blade.php',
        'layout.blade.php',
        'menuData.blade.php',
        'navbar.blade.php',
        'submenu.blade.php',
        'verticalMenu.blade.php',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tree = new TreeFilesHelper(resource_path('views/admin'));
        $tree->setFilter();
        $tree = $tree->buildTree();
        $result = $this->recursiveTree($tree);

        file_put_contents(resource_path('adminSearchIndexed.json'), json_encode($result));
    }

    private function extractPlainTextFromBladeTemplate($fullFileName)
    {
        $templateContent = file_get_contents($fullFileName);

        $sectionPattern = '/(?:@section\(.content.\)\n?)(.*?)(?=\n?@endsection)/is';
        if (preg_match($sectionPattern, $templateContent, $matches)) {
            $sectionContent = $matches[1];
        } else {
            $sectionContent = $templateContent;
        }

        $strippedContent = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $sectionContent);
        $strippedContent = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $strippedContent);
        $strippedContent = preg_replace('/<\?php(.*?)\?>/is', '', $strippedContent);
        $strippedContent = preg_replace('/{{(.*?)}}/is', '', $strippedContent);
        $strippedContent = preg_replace('/@if(.*?)@endif/is', '', $strippedContent);
        $strippedContent = preg_replace('/@foreach(.*?)@endforeach/is', '', $strippedContent);

        $strippedContent = strip_tags($strippedContent);
        $strippedContent = preg_replace('/\s+/', ' ', $strippedContent);

        return $strippedContent;
    }

    private function recursiveTree($node, $deep = 0): array
    {
        $data = [];

        if ($deep == 0 && !empty($node['dirs'])){
            foreach ($node['dirs'] as $key => $value) {
                $subData = $this->recursiveTree($value, $deep+1);
                foreach ($subData as $subDataKey => $subDataValue)
                {
                    if (isset($data[$subDataKey])){
                        $data[$subDataKey] .= $subDataValue;
                    } else {
                        $data[$subDataKey] = $subDataValue;
                    }
                }
            }
        }

        if (!empty($node['files'])){
            foreach ($node['files'] as $key => $value) {
                if (!in_array($value['filename'], $this->excludeFiles))
                {
                    $content = $this->extractPlainTextFromBladeTemplate(resource_path('views\\admin\\'.$value["relative_path"].'\\'.$value['filename']));
                    if (isset($data[$node['folder']])){
                        $data[$node['folder']] .= $content;
                    } else {
                        $data[$node['folder']] = $content;
                    }
                }
            }
        }

        return $data;
    }
}
