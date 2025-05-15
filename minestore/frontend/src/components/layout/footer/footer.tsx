'use client';

import { FC } from 'react';
import { TSettings } from '@/types/settings';
import Image from 'next/image';
import Link from 'next/link';
import { Container } from '@/components/base/container/container';
import { useTranslations } from 'next-intl';
import { ReactSVG } from 'react-svg';
import { ModeToggle } from '@layout/theme-selector/theme-selector';
import { getCacheBuster } from '@helpers/cache-buster';

export type FooterProps = {
    settings: TSettings;
};

export const Footer: FC<FooterProps> = ({ settings }) => {
    return (
        <div className="bg-card/60">
            <Container className="-mt-8 grid grid-cols-1 items-start gap-8 py-20 lg:grid-cols-3">
                <UsefulLinks settings={settings} />
                <Copyright settings={settings} />
                <AboutUs settings={settings} />
            </Container>
        </div>
    );
};

function UsefulLinks({ settings }: { settings: TSettings }) {
    const t = useTranslations('footer');

    if (!settings.footer) {
        return null;
    }

    return (
        <div className="flex flex-col items-center justify-center gap-6 text-center lg:mt-24">
            <div>
                <h3 className="text-2xl font-bold text-card-foreground md:text-3xl">
                    {t('useful-links')}
                </h3>
                <hr className="mx-auto mt-2 h-1 w-12 rounded border-0 bg-primary" />
            </div>
            <ul className="space-y-2">
                <IPInfo/>
                {settings.footer.map((item, index) => (
                    <li key={index}>
                        <Link href={item.url}>{item.name}</Link>
                    </li>
                ))}
            </ul>
        </div>
    );
}

function Copyright({ settings }: { settings: TSettings }) {
    const t = useTranslations('footer');
    const cacheBuster = getCacheBuster();
    return (
        <div className="flex flex-col items-center justify-center gap-6 text-center">
            <Image
                className="aspect-square w-[260px] object-contain"
                src={`${process.env.NEXT_PUBLIC_API_URL}/img/logo.png?${cacheBuster}`}
                width={260}
                height={231}
                alt="Logo"
            />
            <h3 className="text-2xl font-bold text-card-foreground md:text-3xl">
                <span className="block text-sm">&copy; {new Date().getFullYear()}</span>
                {settings.website_name}
            </h3>
            <div>
                <p>
                    <span className="font-bold">{t('all-rights-reserved')} </span>
                    {t('not-affiliated')}
                </p>
                <p>
                    {t('powered-by')}&nbsp;
                    <Link href="https://minestorecms.com/" className="font-bold text-primary">
                        MineStoreCMS Software
                    </Link>
                </p>
            </div>
            <SocialIcons settings={settings} />
            <ModeToggle />
        </div>
    );
}

function AboutUs({ settings }: { settings: TSettings }) {
    const t = useTranslations('footer');

    const { website_name } = settings;

    return (
        <div className="hidden flex-col items-center justify-center gap-6 text-center lg:mt-24 lg:flex">
            <div>
                <h3 className="text-2xl font-bold text-card-foreground md:text-3xl">
                    {t('about-us')}
                </h3>
                <hr className="mx-auto mt-2 h-1 w-12 rounded border-0 bg-primary" />
            </div>
            <p className="text-balance">
                {website_name} {t('description')}
            </p>
            <div className='flex-row gap-6'>
                <Link
                    href={'https://discord.gg/alumenator'}
                    className="hidden items-center transition duration-300 hover:scale-110 lg:flex"
                >
                    <ReactSVG className="h-12 w-12 text-primary" src="/icons/discord.svg" style={{ padding: '15% 0' }}/>
                </Link>
                <Link
                    href={'https://www.youtube.com/@AlumenatoR'}
                    className="hidden items-center transition duration-300 hover:scale-110 lg:flex"
                >
                    <ReactSVG className="h-12 w-12 text-primary" src="/icons/youtube.svg" style={{ padding: '15% 0' }}/>
                </Link>
                <Link
                    href={'https://forum.alumenator.net'}
                    className="hidden items-center transition duration-300 hover:scale-110 lg:flex"
                >
                    <ReactSVG className="h-12 w-12 text-primary" src="/icons/comments.svg" style={{ padding: '15% 0' }}/>
                </Link>
                <Link
                    href={'https://vk.com/alumenator'}
                    className="hidden items-center transition duration-300 hover:scale-110 lg:flex"
                >
                    <ReactSVG className="h-12 w-12 text-primary" src="/icons/vk.svg" />
                </Link>
                <Link
                    href={'https://t.me/AlumenatoR'}
                    className="hidden items-center transition duration-300 hover:scale-110 lg:flex"
                >
                    <ReactSVG className="h-12 w-12 text-primary" src="/icons/telegram.svg"/>
                </Link>
            </div>
        </div>
    );
}

function SocialIcons({ settings }: { settings: TSettings }) {
    const socials = settings.socials;

    return (
        <div className="flex flex-wrap items-center gap-4">
            {Object.entries(socials).map(([key, value], index) => (
                <Link key={index} href={value} target="_blank" rel="noopener noreferrer">
                    <ReactSVG
                        src={`/icons/${key}.svg`}
                        width={32}
                        height={32}
                        beforeInjection={(svg) => {
                            svg.classList.add('w-8', 'h-8');
                        }}
                    />
                </Link>
            ))}
        </div>
    );
}

function IPInfo() {
    return (
        <div>
            ИП Трофимов Евгений Валерьевич <br/>
            ИНН 780442025717 <br/>
            ОГРН 321784700202193 <br/>
            <a href="mailto:support@alumenator.net">support@alumenator.net</a><br/><br/>
            <Link className="font-bold text-primary" href="https://cdn.alumenator.net/legal/offer.pdf">Пользовательское соглашение</Link> <br/>
            <Link className="font-bold text-primary" href="https://cdn.alumenator.net/legal/privacy.pdf">Политика конфиденциальности</Link> <br/>
            <Link className="font-bold text-primary" href="https://cdn.alumenator.net/legal/description.pdf">Описание товаров и услуг</Link>
        </div>
    )
}
