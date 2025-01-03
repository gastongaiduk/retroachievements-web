import type { FC, ReactNode } from 'react';

import { baseButtonVariants } from '@/common/components/+vendor/BaseButton';
import { InertiaLink } from '@/common/components/InertiaLink';
import { cn } from '@/common/utils/cn';
import type { TranslatedString } from '@/types/i18next';

interface ReportIssueOptionItemProps {
  t_buttonText: TranslatedString;
  children: ReactNode;
  href: string;

  anchorClassName?: string;
  /** Set this to truthy if we're navigating to a React page. */
  shouldUseClientSideRoute?: boolean;
}

export const ReportIssueOptionItem: FC<ReportIssueOptionItemProps> = ({
  t_buttonText,
  children,
  href,
  anchorClassName,
  shouldUseClientSideRoute = false,
}) => {
  const AnchorTag = shouldUseClientSideRoute ? InertiaLink : 'a';

  return (
    <li className="flex w-full flex-col items-center justify-between gap-2 rounded bg-embed px-3 py-2 sm:flex-row">
      <p>{children}</p>

      <div className="self-end sm:self-auto">
        <AnchorTag href={href} className={cn(baseButtonVariants({ size: 'sm' }), anchorClassName)}>
          {t_buttonText}
        </AnchorTag>
      </div>
    </li>
  );
};
