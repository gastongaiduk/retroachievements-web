import type { FC } from 'react';

import { RecentPostsBreadcrumbs } from './RecentPostsBreadcrumbs';
import { RecentPostsCards } from './RecentPostsCards';
import { RecentPostsPagination } from './RecentPostsPagination';
import { RecentPostsTable } from './RecentPostsTable';

export const RecentPostsMainRoot: FC = () => {
  return (
    <div>
      <RecentPostsBreadcrumbs />

      <h1 className="w-full">Recent Posts</h1>

      <div className="lg:hidden">
        <RecentPostsCards />
      </div>

      <div className="hidden lg:block">
        <RecentPostsTable />
      </div>

      <div className="mt-2 flex w-full justify-end">
        <RecentPostsPagination />
      </div>
    </div>
  );
};
