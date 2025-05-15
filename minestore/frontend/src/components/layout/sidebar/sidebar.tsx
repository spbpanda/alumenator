import { FC } from 'react';
import { TCategories } from '@/types/categories';
import { CategoryMenu } from './parts/category-menu/category-menu';
import { ExtraWidget } from './parts/extra-widget/extra-widget';

type SidebarProps = {
    categories: TCategories; // Только categories
};

export const Sidebar: FC<SidebarProps> = ({ categories }) => {
    return (
        <div className="w-full flex-col lg:w-[320px] xl:w-[400px]">
            <CategoryMenu categories={categories} />
            <ExtraWidget />
        </div>
    );
};