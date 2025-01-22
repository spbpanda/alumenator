import { Server } from "./Server";

export interface Good {
    id: number;
    name: string;
    description: string;
    image: string;
    type: string;
    price: number;
    servers: Server[];
    additionalImages?: string[];
    rating?: number;
}