import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    const target = normalizeUrlPath(toUrl(urlToCheck) as string);
    const current = normalizeUrlPath(currentUrl);

    if (target === '/') return current === '/';

    // Active if exact match or if the current path is a subpath of target
    return current === target || current.startsWith(target + '/');
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

function normalizeUrlPath(url: string): string {
    if (!url) return '/';

    let path = url;

    // If absolute URL, strip protocol and host
    const schemeIndex = path.indexOf('://');
    if (schemeIndex !== -1) {
        const pathStart = path.indexOf('/', schemeIndex + 3);
        path = pathStart !== -1 ? path.substring(pathStart) : '/';
    }

    // Strip hash and query
    const hashIndex = path.indexOf('#');
    if (hashIndex !== -1) path = path.substring(0, hashIndex);
    const queryIndex = path.indexOf('?');
    if (queryIndex !== -1) path = path.substring(0, queryIndex);

    // Ensure leading slash
    if (path.length === 0 || path[0] !== '/') path = '/' + path;

    // Remove trailing slashes except for root
    if (path.length > 1) path = path.replace(/\/+$/, '');

    return path;
}
