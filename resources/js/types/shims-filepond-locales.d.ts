declare module 'filepond/locale/ar-ar';
declare module 'filepond/locale/fr-fr';
declare module 'filepond/locale/es-es';

declare global {
  interface Window {
    route?: (name: string, params?: any) => string
  }
}
export {}
