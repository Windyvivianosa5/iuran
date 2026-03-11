import { HTMLAttributes } from 'react';

export default function AppLogoIcon({ className, ...props }: HTMLAttributes<HTMLImageElement>) {
    return (
        <img
            src="/pgri1.png"
            alt="PGRI Logo"
            className={className}
            {...props}
        />
    );
}
