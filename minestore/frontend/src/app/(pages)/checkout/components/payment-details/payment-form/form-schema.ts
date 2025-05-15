import { z } from 'zod';

const detailsSchema = z.object({
    fullname: z.string().min(2, {
        message: 'Полное имя должно содержать не менее 2 символов.'
    }),
    email: z.string().email({
        message: 'Пожалуйста, введите действительный адрес электронной почты.'
    }),
    address1: z.string().min(2, {
        message: 'Адрес должен содержать не менее 2 символов.'
    }),
    address2: z.string().optional(),
    city: z.string().min(2, {
        message: 'Город должен состоять как минимум из 2 символов.'
    }),
    region: z.string().min(1, {
        message: 'Регион должен содержать не менее 2 символов.'
    }),
    country: z.string().min(1, {
        message: 'Выберите страну.'
    }),
    zipcode: z.string().min(1, {
        message: 'Почтовый индекс должен содержать не менее 2 символов.'
    })
});

export const paymentFormSchema = z.object({
    details: z.optional(detailsSchema),
    termsAndConditions: z.literal(true, {
        errorMap: () => ({ message: 'Вы должны принять Правила и условия' })
    }),
    privacyPolicy: z.literal(true, {
        errorMap: () => ({ message: 'Вы должны принять Политику конфиденциальности' })
    }),
    paymentMethod: z.string().min(1, {
        message: 'Выберите способ оплаты.'
    })
});

export type PaymentFormValues = z.infer<typeof paymentFormSchema>;

export const defaultValues: Partial<PaymentFormValues> = {
    paymentMethod: ''
};
