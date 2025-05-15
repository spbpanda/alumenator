export type TCheckoutRequest = {
    currency: string;
    paymentMethod: string;
    details?: {
        fullname: string;
        email: string;
        address1: string;
        address2?: string;
        city: string;
        country: string;
        region: string;
        zipcode: string;
        items?: Item[];
    };
    termsAndConditions: boolean;
    privacyPolicy: boolean;
    discordId?: string | null;
};

export type Item = {
    name: string;
    price: number;
    quantity: number;
    amount: number;
    paymentMethod: string;
    paymentObject: string;
    tax: string;
    measurementUnit: string;
}
