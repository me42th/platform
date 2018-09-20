<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Category;

use Orchid\Screen\Link;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Press\Models\Category;
use App\Orchid\Layouts\Category\CategoryListLayout;

class CategoryListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'platform::systems/category.title';
    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'platform::systems/category.description';

    /**
     * Query data.
     *
     * @return array
     */
    public function query() : array
    {
        $categories = Category::where('parent_id', null)->with('allChildrenTerm')->get();
        $allCategories = collect();

        foreach ($categories as $category) {
            $allCategories = $allCategories->merge($this->getCategory($category));
        }

        return [
            'category' => $allCategories, //Category::paginate(),
        ];
    }

    private function getCategory(Category $category, $delimiter = '')
    {
        $result = collect();
        $category->delimiter = $delimiter;
        $result->push($category);

        if ($category->allChildrenTerm()->count()) {
            foreach ($category->allChildrenTerm()->get() as $item) {
                $result = $result->merge($this->getCategory($item, $delimiter.'-'));
            }
        }

        return $result;
    }

    /**
     * Button commands.
     *
     * @return array
     */
    public function commandBar() : array
    {
        return [
            Link::name(trans('platform::common.commands.add'))
                ->icon('icon-plus')
                ->method('create'),
        ];
    }

    /**
     * Views.
     *
     * @return array
     */
    public function layout() : array
    {
        return [
            CategoryListLayout::class,
        ];
    }

    /**
     * @param Request $request
     *
     * @return null
     */
    public function create()
    {
        return redirect()->route('platform.systems.category.create');
    }
}