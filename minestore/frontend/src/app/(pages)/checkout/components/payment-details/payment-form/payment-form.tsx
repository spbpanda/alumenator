'use client';

import { getEndpoints } from '@/api';
import { fetcher } from '@/api/client/fetcher';
import { zodResolver } from '@hookform/resolvers/zod';
import { FormProvider, useForm } from 'react-hook-form';

import { useCartStore } from '@/stores/cart';
import { PaymentFormValues, defaultValues, paymentFormSchema } from './form-schema';
import { useCurrencyStore } from '@/stores/currency';
import { UserDetailsForm } from './user-details-form';
import { useState } from 'react';
import { PaymentFormSubmit } from './payment-form-submit';
import { loadScript } from '@/lib/utils';
import { notify } from '@/core/notifications';
import { QrDetailsProps, QrPaymentModal } from '../../qr-payment-modal';
import { getCookie } from 'cookies-next';
import { PaymentMethodForm } from './payment-method-form';

declare global {
    interface Window {
      TinkoffPay: unknown;
    }
  }

const { checkout } = getEndpoints(fetcher);

export function PaymentForm() {
    const { items } = useCartStore();
    const { currency } = useCurrencyStore();

    const [showQrModal, setShowQrModal] = useState(false);
    const [loading, setLoading] = useState(false);

    const form = useForm<PaymentFormValues>({
        resolver: zodResolver(paymentFormSchema),
        defaultValues,
        mode: 'onSubmit'
    });

    const [qrPaymentDetails, setQrPaymentDetails] = useState<QrDetailsProps['details'] | null>(
        null
    );

    async function onSubmit(data: PaymentFormValues) {
        const paymentMethod = form.getValues('paymentMethod');

        if (paymentMethod === 'Stripe') {
            loadScript('https://js.stripe.com/v3/')
                .then(() => {
                    console.log('Stripe loaded');
                })
                .catch((err) => {
                    console.error('Failed to load Stripe', err);
                });
        }
        if (paymentMethod === 'RazorPay') {
            loadScript('https://checkout.razorpay.com/v1/checkout.js')
                .then(() => {
                    console.log('RazorPay loaded');
                })
                .catch((err) => {
                    console.error('Failed to load RazorPay', err);
                });
        }
        if (paymentMethod === 'TBank') {
            loadScript('https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js')
                .then(() => {
                    console.log('TBank loaded');
                })
                .catch((err) => {
                    console.error('Failed to load TBank', err);
                });
        }

        try {
            setLoading(true);

            const discordId = getCookie('discord_id');

            const response = await checkout({
                currency: currency?.name || 'USD',
                paymentMethod: paymentMethod || 'PayPal',
                details: {
                    ...data.details!, 
                    fullname: 'John Doe', 
                    address1: '123 Main Street', 
                    city: 'New York', 
                    region: 'NY', 
                    country: 'United States', 
                    zipcode: '10001',
                    items: items.map(item => ({
                        name: item.name,
                        price: item.price,
                        quantity: item.count,
                        amount: item.price,
                        paymentMethod: "full_prepayment",
                        paymentObject: "commodity",
                        tax: "none",
                        measurementUnit: "pc"
                    }))
                },
                termsAndConditions: data.privacyPolicy,//data.termsAndConditions,
                privacyPolicy: data.privacyPolicy,
                discordId: discordId || null
            });

            if (!response.success) {
                notify(response.message, 'red');
            }

            if (response.success) {
                if (response.data.type === 'url') {
                    window.location.href = response.data.url;
                } else if (response.data.type === 'html') {
                    const paymentForm = document.createElement('div');
                    paymentForm.innerHTML = response.data.html;
                    document.body.appendChild(paymentForm);
                    paymentForm.querySelector('form')?.submit();
                } else if (response.data.type === 'qrcode') {
                    setQrPaymentDetails({ ...response.data.details });
                    setShowQrModal(true);
                }
            }
        } catch (error) {
            console.error('Error while processing payment:', error);
        } finally {
            setLoading(false);
        }
    }

    if (!items.length) return null;

    return (
        <>
            {qrPaymentDetails ? (
                <QrPaymentModal
                    show={showQrModal}
                    onHide={() => setShowQrModal(false)}
                    details={qrPaymentDetails}
                />
            ) : null}
            <FormProvider {...form}>
                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
                    <UserDetailsForm />

                    <PaymentMethodForm items={items} />

                    <PaymentFormSubmit loading={loading} onSubmit={() => onSubmit(form.getValues())} />
                </form>
            </FormProvider>
        </>
    );
}
