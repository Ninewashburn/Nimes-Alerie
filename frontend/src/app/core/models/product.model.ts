export interface Product {
  id: number;
  title: string;
  description: string;
  price: string;
  quantity: number;
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
  parent: string;
}

export interface Article {
  id: number;
  name: string;
  content: string;
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
  country?: string;
  roles: string[];
}

export interface Order {
  id: number;
  createdAt: string;
  status: string;
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
}

export interface Post {
  id: number;
  content: string;
  createdAt: string;
  upVote: number;
  downVote: number;
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
