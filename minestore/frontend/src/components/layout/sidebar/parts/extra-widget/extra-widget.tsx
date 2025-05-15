'use client';

import { FC } from 'react';
import { RecentPurchases } from '@layout/recent-purchases/recent-purchases';
import { useTranslations } from 'next-intl';
import { Rocket } from 'lucide-react';

type ExtraWidgetProps = {
    // Пропсы не требуются
};

export const ExtraWidget: FC<ExtraWidgetProps> = () => {
    const t = useTranslations();

    return (
        <div className="mt-4 hidden w-full rounded-[10px] bg-card p-8 lg:block">
            <div className="relative mb-4 flex items-center justify-center gap-2 rounded-[10px] bg-accent py-4 font-bold">
                <h3 className="text-accent-foreground">{t('recent-purchases')}</h3>
                <Rocket className="text-accent-foreground" />
            </div>
            <RecentPurchases limit={10} />
        </div>
    );
};