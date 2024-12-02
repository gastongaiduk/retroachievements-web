import type { Alpine } from 'alpinejs';
import type { IconType } from 'react-icons/lib';
import type { route as routeFn } from 'ziggy-js';

import type {
  modalComponent as ModalComponent,
  navbarSearchComponent as NavbarSearchComponent,
  newsCarouselComponent as NewsCarouselComponent,
  toggleAchievementRowsComponent as ToggleAchievementRowsComponent,
  tooltipComponent as TooltipComponent,
} from '@/tall-stack/alpine';
import type { autoExpandTextInput as AutoExpandTextInput } from '@/tall-stack/utils/autoExpandTextInput';
import type {
  deleteCookie as DeleteCookie,
  getCookie as GetCookie,
  setCookie as SetCookie,
} from '@/tall-stack/utils/cookie';
import type { fetcher as Fetcher } from '@/tall-stack/utils/fetcher';
import type { getStringByteCount as GetStringByteCount } from '@/tall-stack/utils/getStringByteCount';
import type { handleLeaderboardTabClick as HandleLeaderboardTabClick } from '@/tall-stack/utils/handleLeaderboardTabClick';
import type { initializeTextareaCounter as InitializeTextareaCounter } from '@/tall-stack/utils/initializeTextareaCounter';
import type { injectShortcode as InjectShortcode } from '@/tall-stack/utils/injectShortcode';
import type { loadPostPreview as LoadPostPreview } from '@/tall-stack/utils/loadPostPreview';
import type { toggleUserCompletedSetsVisibility as ToggleUserCompletedSetsVisibility } from '@/tall-stack/utils/toggleUserCompletedSetsVisibility';
import type { updateUrlParameter as UpdateUrlParameter } from '@/tall-stack/utils/updateUrlParameter';

interface PlausibleEventOptions {
  callback?: () => void;
  props?: Record<string, string | boolean | number>;
}

declare global {
  // Alpine.js
  var Alpine: Alpine;
  var assetUrl: string;
  var autoExpandTextInput: typeof AutoExpandTextInput;
  var cachedDialogHtmlContent: string | undefined;
  var cfg: Record<string, unknown> | undefined;
  var copyToClipboard: (text: string) => void;
  var deleteCookie: typeof DeleteCookie;
  var fetcher: typeof Fetcher;
  var getCookie: typeof GetCookie;
  var getStringByteCount: typeof GetStringByteCount;
  var handleLeaderboardTabClick: typeof HandleLeaderboardTabClick;
  var initializeTextareaCounter: typeof InitializeTextareaCounter;
  var injectShortcode: typeof InjectShortcode;
  var loadPostPreview: typeof LoadPostPreview;
  var modalComponent: typeof ModalComponent;
  var newsCarouselComponent: typeof NewsCarouselComponent;
  var navbarSearchComponent: typeof NavbarSearchComponent;
  var setCookie: typeof SetCookie;
  var showStatusFailure: (message: string) => void;
  var showStatusSuccess: (message: string) => void;
  var toggleAchievementRowsComponent: typeof ToggleAchievementRowsComponent;
  var toggleUserCompletedSetsVisibility: typeof ToggleUserCompletedSetsVisibility;
  var tooltipComponent: typeof TooltipComponent;
  var updateUrlParameter: typeof UpdateUrlParameter;

  // Inertia
  var route: typeof routeFn;

  // Plausible
  var plausible: ((eventName: string, options?: PlausibleEventOptions) => void) | undefined;
}

declare module '@tanstack/react-table' {
  // eslint-disable-next-line @typescript-eslint/no-unused-vars -- this is valid
  interface ColumnMeta<TData, TValue> {
    t_label: string;

    align?: 'right' | 'left';
    Icon?: IconType;
    sortType?: 'default' | 'date' | 'quantity' | 'boolean';
  }
}
