export type Language = 'id' | 'en';

export interface ProjectMetric {
  label: string;
  value: string;
}

export interface Project {
  id: string;
  title: string;
  tagline: string;
  description: string;
  longDescription: string;
  category: 'Full-Stack' | 'Frontend' | 'UI/UX & Systems' | 'Open Source' | 'AI & Tools';
  role: string;
  timeline: string;
  client?: string;
  image: string;
  tags: string[];
  metrics: ProjectMetric[];
  highlights: string[];
  techStack: {
    frontend: string[];
    backend?: string[];
    database?: string[];
    cloudAndDevOps?: string[];
  };
  demoUrl?: string;
  githubUrl?: string;
  featured: boolean;
  colorGradient: string;
  accentColor: string;
}

export interface ArticleSection {
  heading?: string;
  body: string;
  codeSnippet?: {
    language: string;
    code: string;
    filename?: string;
  };
  tip?: string;
}

export interface BlogPost {
  id: string;
  title: string;
  slug: string;
  summary: string;
  category: 'Frontend Architecture' | 'Performance' | 'Clean Code' | 'Full-Stack' | 'Career & Insights';
  tags: string[];
  readTime: string;
  publishedAt: string;
  coverImage: string;
  author: {
    name: string;
    role: string;
    avatar: string;
  };
  likes: number;
  views: string;
  sections: ArticleSection[];
}

export interface Experience {
  id: string;
  period: string;
  role: string;
  company: string;
  location: string;
  type: 'Full-Time' | 'Contract' | 'Remote';
  description: string;
  achievements: string[];
  skills: string[];
  featured?: boolean;
}

export interface SkillItem {
  name: string;
  category: 'frontend' | 'backend' | 'devops' | 'tools';
  level: number; // 1-100
  experience: string;
  iconName: string;
  highlightText: string;
}

export interface Testimonial {
  id: string;
  name: string;
  role: string;
  company: string;
  avatar: string;
  content: string;
  rating: number;
  projectTag: string;
}

export interface SocialLink {
  platform: string;
  name: string;
  url: string;
  username: string;
  icon: string;
  bgColor: string;
  textColor: string;
  description: string;
}

export interface CommentItem {
  id: string;
  author: string;
  avatar: string;
  date: string;
  text: string;
}
