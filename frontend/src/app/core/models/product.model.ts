export interface Product {
  id: number;
  title: string;
  description: string;
  priceHT: string;
  priceTTC: string;
  quantity: number;
  isActive: boolean;
  image?: string;
  cover?: string;
  brand?: Brand;
  category?: Category[];
}

export interface Brand {
  id: number;
  name: string;
  logo?: string;
}

export interface Category {
  id: number;
  title: string;
  description: string;
  parent?: Category | null;
  children?: Category[];
}

export interface Article {
  id: number;
  name: string;
  content: string;
  cover?: string;
}

export interface User {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
  telephone?: string;
  birthAt: string;
  address: string;
  secondAddress?: string;
  city: string;
  postalCode?: string;
  country?: string;
  gender?: string;
  roles: string[];
}

export interface Order {
  id: number;
  createdAt: string;
  status: 'pending' | 'preparing' | 'shipped' | 'refunded';
  total?: string;
  items?: CheckoutItem[];
  billNumber?: string;
  payment?: 'card' | 'paypal';
}

export interface CheckoutItem {
  productId: number;
  title: string;
  quantity: number;
  priceTTC: string;
}

export interface CheckoutRequest {
  items: CheckoutItem[];
  deliveryAddress: string;
  deliveryCity: string;
  deliveryPostalCode: string;
  deliveryCountry: string;
  paymentMethod: 'card' | 'paypal';
  total: number;
}

export interface OrderConfirmation {
  id: number;
  billNumber: string;
  status: string;
  total: string;
  itemsCount: number;
  createdAt: string;
  payment: string;
}

export interface Rate {
  id: number;
  rate: number;
  testimonial: string;
}

export interface ForumType {
  id: number;
  name: string;
  description: string;
}

export interface SubType {
  id: number;
  name: string;
  description: string;
}

export interface Thread {
  id: number;
  subject: string;
  createdAt: string;
  user?: User;
  subtype?: string | SubType;
}

export interface Post {
  id: number;
  content: string;
  createdAt: string;
  upVote: number;
  downVote: number;
  thread?: string | Thread;
  user?: User;
}

export interface CartItem {
  product: Product;
  quantity: number;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface JwtToken {
  token: string;
}

export interface ApiCollection<T> {
  'hydra:member': T[];
  'hydra:totalItems': number;
}
