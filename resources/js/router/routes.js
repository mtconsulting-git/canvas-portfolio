import AllStats from "../views/AllStats";
import EditPost from "../views/EditPost";
import EditSettings from "../views/EditSettings";
import EditTag from "../views/EditTag";
import EditTopic from "../views/EditTopic";
import EditUser from "../views/EditUser";
import PostList from "../views/PostList";
import PostStats from "../views/PostStats";
import TagList from "../views/TagList";
import TopicList from "../views/TopicList";
import UserList from "../views/UserList";
import settings from "../store/modules/settings";
import PortfolioList from "../views/PortfolioList";
import PortfolioCategoryList from "../views/PortfolioCategoryList";
import EditPortfolio from "../views/EditPortfolio";
import EditCategory from "../views/EditCategory";
import PortfolioStats from "../views/PortfolioStats";

let isAdmin = settings.state.user.role === 3;
let blogEnabled = settings.state.blogEnabled;
let portfolioEnabled = settings.state.portfolioEnabled;

export default [
  {
    path: "/",
    name: "home",
    redirect: "/stats",
  },
  {
    path: "/posts",
    name: "posts",
    component: PostList,
    beforeEnter: (to, from, next) => {
      blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/posts/create",
    name: "create-post",
    component: EditPost,
    beforeEnter: (to, from, next) => {
      blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/posts/:id/edit",
    name: "edit-post",
    component: EditPost,
    beforeEnter: (to, from, next) => {
      blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/tags",
    name: "tags",
    component: TagList,
    beforeEnter: (to, from, next) => {
      isAdmin && blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/tags/create",
    name: "create-tag",
    component: EditTag,
    beforeEnter: (to, from, next) => {
      isAdmin && blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/tags/:id/edit",
    name: "edit-tag",
    component: EditTag,
    beforeEnter: (to, from, next) => {
      isAdmin && blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/topics",
    name: "topics",
    component: TopicList,
    beforeEnter: (to, from, next) => {
      isAdmin && blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/topics/create",
    name: "create-topic",
    component: EditTopic,
    beforeEnter: (to, from, next) => {
      isAdmin && blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/topics/:id/edit",
    name: "edit-topic",
    component: EditTopic,
    beforeEnter: (to, from, next) => {
      isAdmin && blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/stats",
    name: "stats",
    component: AllStats,
  },
  {
    path: "/blog/stats/:id",
    name: "post-stats",
    component: PostStats,
    beforeEnter: (to, from, next) => {
      blogEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/portfolios/stats/:id",
    name: "portfolios-stats",
    component: PortfolioStats,
    beforeEnter: (to, from, next) => {
      portfolioEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/portfolios",
    name: "portfolios",
    component: PortfolioList,
    beforeEnter: (to, from, next) => {
      portfolioEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/portfolios/create",
    name: "create-portfolio",
    component: EditPortfolio,
    beforeEnter: (to, from, next) => {
      portfolioEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/portfolios/:id/edit",
    name: "edit-portfolio",
    component: EditPortfolio,
    beforeEnter: (to, from, next) => {
      portfolioEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/portfolio_categories",
    name: "portfolio_categories",
    component: PortfolioCategoryList,
    beforeEnter: (to, from, next) => {
      isAdmin && portfolioEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/portfolio_categories/create",
    name: "create-portfolio_category",
    component: EditCategory,
    beforeEnter: (to, from, next) => {
      isAdmin && portfolioEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/portfolio_categories/:id/edit",
    name: "edit-portfolio_category",
    component: EditCategory,
    beforeEnter: (to, from, next) => {
      isAdmin && portfolioEnabled ? next() : next({ name: "home" });
    },
  },
  {
    path: "/users",
    name: "users",
    component: UserList,
    beforeEnter: (to, from, next) => {
      isAdmin ? next() : next({ name: "home" });
    },
  },
  {
    path: "/users/create",
    name: "create-user",
    component: EditUser,
    beforeEnter: (to, from, next) => {
      isAdmin ? next() : next({ name: "home" });
    },
  },
  {
    path: "/users/:id/edit",
    name: "edit-user",
    component: EditUser,
    beforeEnter: (to, from, next) => {
      if (isAdmin || settings.state.user.id == to.params.id) {
        next();
      } else {
        next({ name: "home" });
      }
    },
  },
  {
    path: "/settings",
    name: "edit-settings",
    component: EditSettings,
  },
  {
    path: "*",
    name: "catch-all",
    redirect: "/stats",
  },
];
