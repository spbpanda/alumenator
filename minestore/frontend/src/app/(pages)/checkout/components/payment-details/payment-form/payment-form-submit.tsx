import { useState, useEffect, useRef, MouseEventHandler } from 'react';
import { FormControl, FormField, FormItem, FormMessage } from '@/components/ui/form';
import { Checkbox } from '@/components/ui/checkbox';
import { Button } from '@/components/ui/button';
import { Loader2, X } from 'lucide-react';
import { useTranslations } from 'next-intl';
import { useSettingsStore } from '@/stores/settings';
import { useCartStore } from '@/stores/cart';
import { getEndpoints } from '@/api';
import { fetcher } from '@/api/client/fetcher';
import { getCookie, setCookie, deleteCookie } from 'cookies-next';
import { ReactSVG } from 'react-svg';
import { notify } from '@/core/notifications';
import { TDiscordAuth } from '@/types/discord-auth';
import Link from 'next/link';

export const PaymentFormSubmit = ({ loading, onSubmit }: { loading: boolean, onSubmit: MouseEventHandler<HTMLButtonElement> | undefined}) => {
   const t = useTranslations('checkout');
   const { settings } = useSettingsStore();
   const { cart } = useCartStore();
   const { getDiscordAuth } = getEndpoints(fetcher);
   const [isDiscordLinked, setIsDiscordLinked] = useState(false);
   const [discordUsername, setDiscordUsername] = useState<string>('');
   const discordWindowRef = useRef<Window | null>(null);

   const isDiscordRequired = Boolean(settings?.discord_sync) && Boolean(cart?.discord_sync);

   useEffect(() => {
      const discordStatus = getCookie('discord_linked');
      const username = getCookie('discord_username');
      setIsDiscordLinked(discordStatus === 'true');
      if (username) {
         setDiscordUsername(username.toString());
      }

      const handleAuthMessage = (event: MessageEvent) => {
         try {
            let authData: TDiscordAuth;
            if (typeof event.data === 'string') {
               authData = JSON.parse(event.data);
            } else {
               authData = event.data;
            }

            if (authData.success && authData.discord_username) {
               console.log('[Discord Auth] Authentication successful');

               if (discordWindowRef.current) {
                  console.log('[Discord Auth] Closing Discord window');
                  discordWindowRef.current.close();
               }

               const cookieOptions: { maxAge: number; path: string; sameSite: 'lax' } = {
                  maxAge: 60 * 60 * 6,
                  path: '/',
                  sameSite: 'lax'
               };

               setCookie('discord_linked', 'true', cookieOptions);

               if (authData.discord_username) {
                  setCookie('discord_username', authData.discord_username, cookieOptions);
                  setDiscordUsername(authData.discord_username);
               }

               if (authData.discord_id) {
                  setCookie('discord_id', authData.discord_id, cookieOptions);
               }

               setIsDiscordLinked(true);

               notify(`Discord account linked: ${authData.discord_username}`, 'green');
            } else if (authData.error) {
               console.log('[Discord Auth] Error in auth data:', authData.message);
               notify(authData.message || 'Failed to link Discord account', 'red');
            }
         } catch (error) {
            notify('Error processing Discord authentication', 'red');
         }
      };

      window.addEventListener('message', handleAuthMessage);

      return () => {
         window.removeEventListener('message', handleAuthMessage);
      };
   }, []);

   const handleDiscordAuth = async () => {
      console.log('[Discord Auth] Starting auth process');
      try {
         const data = await getDiscordAuth();
         if (data.success && data.url) {
            const width = 500;
            const height = 800;
            const left = window.screen.width / 2 - width / 2;
            const top = window.screen.height / 2 - height / 2;

            discordWindowRef.current = window.open(
               data.url,
               'Discord Authorization',
               `width=${width},height=${height},left=${left},top=${top},status=yes,toolbar=no,menubar=no`
            );

            if (discordWindowRef.current === null) {
               console.log('[Discord Auth] Popup blocked');
               notify('Please allow popups for Discord authentication', 'red');
            }
         } else {
            console.log('[Discord Auth] Invalid response:', data);
            notify(data.message || 'Failed to start Discord authentication', 'red');
         }
      } catch (error) {
         console.error('[Discord Auth] Error during auth:', error);
         notify('Failed to authenticate with Discord', 'red');
      }
   };

   const handleUnlinkDiscord = () => {
      try {
         // Remove all Discord-related cookies
         deleteCookie('discord_linked');
         deleteCookie('discord_username');
         deleteCookie('discord_id');

         // Reset state
         setIsDiscordLinked(false);
         setDiscordUsername('');

         notify('Discord account unlinked successfully', 'green');
      } catch (error) {
         console.error('[Discord Auth] Error unlinking account:', error);
         notify('Failed to unlink Discord account', 'red');
      }
   };

   return (
      <div className="flex flex-col w-full">
         <div className="flex flex-col md:flex-row items-center justify-between w-full gap-4">
            <FormField
               name="privacyPolicy"
               render={({ field }) => (
                  <FormItem className="flex flex-row items-start space-x-3 space-y-0">
                     <FormControl>
                        <Checkbox checked={field.value} onCheckedChange={field.onChange} />
                     </FormControl>
                     <div className="space-y-1 leading-none">
                        <CheckoutAgreement />
                        <FormMessage />
                     </div>
                  </FormItem>
               )}
            />

            <div className="flex flex-row gap-2 items-center">
               {isDiscordRequired && (
                  <div className="relative flex items-center">
                     <Button
                        type="button"
                        onClick={handleDiscordAuth}
                        disabled={isDiscordLinked || loading}
                        className="flex items-center justify-center bg-[#7289da] text-white gap-2 w-auto hover:bg-[#5a6fb8] disabled:bg-[#99aab5]"
                        variant={isDiscordLinked ? "outline" : "default"}
                     >
                        <ReactSVG className="w-8 text-white self-center" src="/icons/discord.svg" />
                        <span className="flex items-center">
                           {isDiscordLinked ? `${t('discord_linked')} (${discordUsername})` : t('discord_not_linked')}
                        </span>
                     </Button>
                     {isDiscordLinked && (
                        <Button
                           type="button"
                           onClick={handleUnlinkDiscord}
                           variant="ghost"
                           size="icon"
                           className="ml-2 text-red-100 hover:text-white bg-[#da7272] hover:bg-[#b85a5a]"
                        >
                           <X className="h-4 w-4" />
                        </Button>
                     )}
                  </div>
               )}
               <Button
                  type="submit"
                  className="flex items-center justify-center gap-2 w-auto"
                  onClick={onSubmit}
               >
                  {loading && <Loader2 className="animate-spin" size={24} />}
                  {t('purchase')}
               </Button>
            </div>

         </div>
      </div>
   );
};

const CheckoutAgreement = () => {
   return (
       <p>
           Я согласен с <Link className="link" href='https://cdn.alumenator.net/legal/offer.pdf'>офертой</Link>, <Link className="link" href='https://cdn.alumenator.net/legal/privacy.pdf'>политикой конфиденциальности</Link>.
       </p>
   );
};

export default PaymentFormSubmit;