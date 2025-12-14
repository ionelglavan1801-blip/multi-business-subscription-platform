# Multi-Business Subscription Platform

A SaaS platform built with Laravel 12 that allows users to manage multiple businesses with team collaboration, subscription billing, and project management.

## Features

- **Multi-Business Management** - Users can create and manage multiple businesses
- **Team Collaboration** - Invite team members via email with role-based access (owner, admin, member)
- **Subscription Billing** - Stripe integration with multiple plans (Free, Pro, Enterprise)
- **Project Management** - Create and manage projects per business
- **Plan Limits** - Enforced limits on businesses, team members, and projects per plan
- **Email Notifications** - Subscription activations, cancellations, payment failures
- **Webhook Handling** - Robust Stripe webhook processing

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Database:** MySQL 8.0
- **Cache/Queue:** Redis
- **Payments:** Stripe (Checkout, Webhooks, Customer Portal)
- **Email:** Resend (transactional emails)
- **Frontend:** Blade, Tailwind CSS
- **Infrastructure:** Docker, Nginx, Cloudflare Tunnel

## Architecture

```
app/
├── Actions/           # Single-purpose action classes
│   ├── Billing/       # Stripe checkout, webhook handling
│   └── Business/      # Business creation, invitation logic
├── Http/
│   ├── Controllers/   # Thin controllers
│   ├── Middleware/    # Custom middleware
│   └── Requests/      # Form request validation
├── Models/            # Eloquent models with relationships
├── Notifications/     # Email notifications
├── Policies/          # Authorization policies
└── Services/          # External service integrations
```

## Database Schema

- **Users** - Authentication, profile management
- **Businesses** - Multi-tenant business entities
- **Plans** - Subscription tiers with limits
- **Subscriptions** - Stripe subscription tracking
- **Invitations** - Team member invitations
- **Projects** - Business projects

## Getting Started

### Prerequisites

- Docker & Docker Compose
- PHP 8.2+
- Composer
- Node.js 18+

### Installation

```bash
# Clone repository
git clone https://github.com/ionelglavan1801-blip/multi-business-subscription-platform.git
cd multi-business-subscription-platform

# Copy environment file
cp .env.example .env

# Start Docker containers
docker compose up -d

# Install dependencies
docker compose exec app composer install
docker compose exec app npm install && npm run build

# Setup application
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### Environment Variables

```env
# Stripe
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
STRIPE_PRICE_PRO=price_xxx
STRIPE_PRICE_ENTERPRISE=price_xxx

# Email (Resend)
MAIL_MAILER=resend
RESEND_API_KEY=re_xxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=Business
php artisan test --filter=Invitation
```

**111 tests passing** covering authentication, business management, invitations, and billing flows.

## Subscription Plans

| Plan | Price | Businesses | Team Members | Projects |
|------|-------|------------|--------------|----------|
| Free | $0/mo | 1 | 3 | 3 |
| Pro | $29/mo | 5 | 10 | 50 |
| Enterprise | $99/mo | Unlimited | Unlimited | Unlimited |

## Live Demo

https://multi-business-subscription-platform.ionglavan.com

## License

MIT License
