# Domain Marketplace Platform

A comprehensive Laravel 11-based domain marketplace platform with advanced features including auctions, bidding, escrow payments, and domain verification.

## 🚀 Features

- **User Management**: Registration, authentication, profile verification
- **Domain Listings**: Create, manage, and sell domain names
- **Auction System**: Real-time bidding with auto-extend functionality
- **Buy It Now**: Instant purchase option for domains
- **Domain Verification**: DNS-based ownership verification
- **Payment Processing**: PayPal integration with escrow system
- **Admin Panel**: Comprehensive management dashboard
- **API Endpoints**: RESTful API for mobile app integration

## 🛠️ Installation

1. **Clone and install dependencies**
   ```bash
   git clone <repository-url>
   cd domain-marketplace
   composer install
   npm install
   ```

2. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Start development server**
   ```bash
   php artisan serve
   ```

## 🔧 Configuration

Configure your `.env` file with:
- Database credentials
- PayPal API credentials
- Mail settings
- Site configuration

## 🚀 Usage

### Default Accounts
- **Admin**: admin@flippdeal.com / password
- **Test User**: test@example.com / password

### API Endpoints
- Authentication: `/api/v1/auth/*`
- Domains: `/api/v1/domains/*`
- User Management: `/api/v1/user/*`

## 🔒 Security Features

- Domain verification via DNS records
- Profile verification (PayPal + Government ID)
- Two-factor authentication
- Audit logging
- Role-based access control

## 📊 Key Components

- **Models**: User, Domain, Bid, Order, Verification
- **Services**: DomainVerificationService, PayPalService
- **Notifications**: Email notifications for key events
- **Jobs**: Background processing for notifications
- **API**: RESTful endpoints for mobile integration

## 🚀 Deployment

1. Set up production server with PHP 8.2+, MySQL, Redis
2. Configure environment variables
3. Run migrations and seeders
4. Set up queue workers and cron jobs
5. Configure SSL and optimize assets

## 📄 License

MIT License

---

**Built with Laravel 11**