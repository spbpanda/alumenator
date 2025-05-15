<?php

namespace App\Helpers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Collection;

class SortHelper
{
    /**
     * Update the sorting for the items when an item is moved within the same category.
     * It adjusts the sorting index of other items accordingly.
     *
     * @param Collection &$items
     * @param Item $item
     * @param int $oldIndex
     * @param int $newIndex
     */
    public static function updateItemsSorting(Collection &$items, Item $item, int $oldIndex, int $newIndex): void
    {
        // Getting array of sorting values
        $sortings = $items->pluck('sorting')->sort()->values();

        // Defining real sorting values for indexes
        $oldSortingValue = $sortings[$oldIndex];
        $newSortingValue = $sortings[$newIndex];

        if ($newSortingValue < $oldSortingValue) {
            foreach ($items as $otherItem) {
                if ($otherItem->id === $item->id) {
                    continue;
                }

                if ($otherItem->sorting >= $newSortingValue && $otherItem->sorting < $oldSortingValue) {
                    $otherItem->sorting++;
                    $otherItem->save();
                }
            }
        } else {
            foreach ($items as $otherItem) {
                if ($otherItem->id === $item->id) {
                    continue;
                }

                if ($otherItem->sorting <= $newSortingValue && $otherItem->sorting > $oldSortingValue) {
                    $otherItem->sorting--;
                    $otherItem->save();
                }
            }
        }

        $item->sorting = $newSortingValue;
        $item->save();
    }

    /**
     * Update the sorting for categories when a category is moved within the same category.
     * It adjusts the sorting index of other categories accordingly.
     *
     * @param Collection &$categories
     * @param Category $category
     * @param int $oldIndex
     * @param int $newIndex
     */
    public static function updateCategoriesSorting(Collection &$categories, Category $category, int $oldIndex, int $newIndex): void
    {
        if ($newIndex < $oldIndex) {
            foreach ($categories as $otherCategory) {
                if ($otherCategory->id === $category->id) {
                    continue;
                }

                if ($otherCategory->sorting >= $newIndex && $otherCategory->sorting < $oldIndex) {
                    $otherCategory->sorting++;
                    $otherCategory->save();
                }
            }
        } elseif ($newIndex > $oldIndex) {
            foreach ($categories as $otherCategory) {
                if ($otherCategory->id === $category->id) {
                    continue;
                }

                if ($otherCategory->sorting > $oldIndex && $otherCategory->sorting <= $newIndex) {
                    $otherCategory->sorting--;
                    $otherCategory->save();
                }
            }
        }

        $category->sorting = $newIndex;
        $category->save();
    }

    /**
     * Adjust sorting after an item is moved from a category.
     *
     * @param Collection &$items
     * @param int $oldIndex
     */
    public static function adjustSortingAfterMoving(Collection &$items, int $oldIndex): void
    {
        foreach ($items as $otherItem) {
            if ($otherItem->sorting > $oldIndex) {
                $otherItem->sorting--;
                $otherItem->save();
            }
        }
    }

    /**
     * Move an item into a new index in the target category,
     * adjusting the sorting of other items in the category accordingly.
     *
     * @param Collection $items
     * @param $item
     * @param int $newIndex
     */
    public static function insertItemAtNewIndex(Collection &$items, $item, int $newIndex): void
    {
        foreach ($items as $otherItem) {
            if ($otherItem->sorting >= $newIndex) {
                $otherItem->sorting++;
                $otherItem->save();
            }
        }

        $item->sorting = $newIndex;
        $item->save();
    }

    public static function reorderItemsAndSave(Collection $items): void
    {
        $items->map(function ($item, $key) {
            $item->sorting = $key;
            return $item;
        });

        $items->each(function ($item) {
            $item->save();
        });
    }

    /**
     * Returns column name for search
     * @param string $value
     * @return string
     */
    public static function getSearchType(string $value): string
    {
        // Str::isUuid($value) doesnt work
        if (strlen($value) == 32 && substr_count($value, '-') == 4) return 'uuid';
        if (filter_var($value, FILTER_VALIDATE_IP)) return 'ip';
        return 'username';
    }
}
