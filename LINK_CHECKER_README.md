# 🔗 Laravel Link Checker - Comprehensive Link Validation System

A powerful Laravel Artisan command and PHPUnit test suite for automatically checking all links in your website for broken URLs, HTTP errors, and accessibility issues.

## 🚀 Features

### ✅ **Comprehensive Link Discovery**
- **Route Scanning**: Automatically discovers all GET routes in your Laravel application
- **Blade View Analysis**: Extracts links from all Blade templates (`<a href="">`, `route()`, `url()`)
- **Controller Link Detection**: Finds route references in controller files
- **Smart URL Normalization**: Handles relative, absolute, and protocol-relative URLs

### ✅ **Advanced Link Validation**
- **HTTP Status Checking**: Validates 200 OK, detects 404, 500, redirects
- **Concurrent Processing**: Configurable concurrent requests for faster checking
- **Timeout Handling**: Customizable timeout settings for slow responses
- **Error Classification**: Categorizes errors (timeouts, network issues, server errors)

### ✅ **Flexible Filtering Options**
- **Internal vs External**: Option to check only internal or external links
- **Skip Invalid URLs**: Automatically skips javascript:, mailto:, tel:, # links
- **Route Parameter Handling**: Smart handling of Laravel route parameters

### ✅ **Rich Reporting & Export**
- **Detailed Console Output**: Beautiful progress bars and color-coded results
- **Summary Statistics**: Working vs broken links with percentages
- **CSV Export**: Export results to CSV for spreadsheet analysis
- **JSON Export**: Export results to JSON for programmatic processing
- **Error Details**: Specific error messages and HTTP status codes

## 📋 Installation & Usage

### **Option A: Laravel Artisan Command**

```bash
# Basic link check
php artisan check:links

# Check only internal links
php artisan check:links --skip-external

# Check only external links
php artisan check:links --skip-internal

# Export results to CSV
php artisan check:links --export=csv

# Export results to JSON
php artisan check:links --export=json

# Custom timeout and concurrency
php artisan check:links --timeout=60 --concurrent=10
```

### **Option B: PHPUnit Test Suite**

```bash
# Run all link check tests
php artisan test --filter=LinkCheckTest

# Run specific test
php artisan test --filter=test_internal_routes_are_accessible

# Run with verbose output
php artisan test --filter=LinkCheckTest -v
```

## 🛠️ Command Options

| Option | Description | Default |
|--------|-------------|---------|
| `--export=` | Export results to file (csv, json) | None |
| `--timeout=` | HTTP request timeout in seconds | 30 |
| `--concurrent=` | Number of concurrent requests | 5 |
| `--skip-external` | Skip external links | false |
| `--skip-internal` | Skip internal links | false |

## 📊 Sample Output

```
🔍 Starting comprehensive link check for FlippDeal...

Base URL: http://localhost:8000

📋 Collecting links from routes and views...
  📍 Scanning routes...
  ✅ Found 45 route links
  📄 Scanning Blade views...
  ✅ Found 127 links in views
  🎮 Scanning controllers...
  ✅ Found 23 route links in controllers

Found 195 unique links to check

🔗 Checking links...
████████████████████████████████████████ 100%

📊 Link Check Results Summary
================================
| Status     | Count | Percentage |
|------------|-------|------------|
| ✅ Working | 180   | 92.3%      |
| ❌ Broken  | 8     | 4.1%       |
| 🔄 Redirects| 5     | 2.6%       |
| ⏱️  Timeouts| 2     | 1.0%       |
| ⚠️  Errors  | 0     | 0.0%       |
| 📊 Total   | 195   | 100%       |

❌ Broken Links:
================
  http://localhost:8000/old-page - HTTP 404
  http://localhost:8000/broken-link - HTTP 500

📁 Results exported to: /path/to/storage/app/link_check_results_2024-01-15_14-30-25.csv
```

## 🔧 Configuration

### **Environment Variables**

Add to your `.env` file:

```env
# Base URL for link checking
APP_URL=http://localhost:8000

# Optional: Custom timeout for HTTP requests
LINK_CHECK_TIMEOUT=30

# Optional: Number of concurrent requests
LINK_CHECK_CONCURRENT=5
```

### **Customizing Link Discovery**

The command automatically discovers links from:

1. **Laravel Routes**: All GET routes (excluding API routes)
2. **Blade Views**: `<a href="">`, `{{ route() }}`, `{{ url() }}`
3. **Controllers**: `route()` helper calls

### **Excluding Routes**

To exclude specific routes from checking, modify the `collectLinksFromRoutes()` method:

```php
// Skip specific routes
if (in_array($uri, ['admin/sensitive', 'api/private'])) {
    continue;
}
```

## 📁 File Structure

```
app/Console/Commands/
├── CheckLinksCommand.php          # Main Artisan command

tests/Feature/
├── LinkCheckTest.php              # PHPUnit test suite

storage/app/
├── link_check_results_*.csv       # Exported CSV results
├── link_check_results_*.json      # Exported JSON results
```

## 🧪 Test Coverage

The PHPUnit test suite includes:

- **Route Accessibility**: Tests all internal routes return valid responses
- **Critical Routes**: Ensures essential pages (login, dashboard) are accessible
- **Authentication Protection**: Verifies protected routes redirect properly
- **Link Extraction**: Tests link discovery from view files
- **URL Validation**: Ensures extracted links are valid URLs

## 🚨 Error Handling

### **Common Issues & Solutions**

1. **Route Parameter Errors**
   ```bash
   # Routes with required parameters are automatically handled
   # Example: /domains/{id} becomes /domains/1
   ```

2. **Authentication Required Routes**
   ```bash
   # Protected routes will return 302 redirects (expected)
   # Use --skip-internal to focus on public routes only
   ```

3. **External Link Failures**
   ```bash
   # Use --skip-external to focus on internal links only
   # External sites may block automated requests
   ```

4. **Timeout Issues**
   ```bash
   # Increase timeout for slow servers
   php artisan check:links --timeout=60
   ```

## 📈 Performance Optimization

### **Concurrent Requests**
```bash
# Increase concurrency for faster checking (be respectful!)
php artisan check:links --concurrent=10

# Reduce concurrency for rate-limited servers
php artisan check:links --concurrent=2
```

### **Selective Checking**
```bash
# Check only internal links (faster)
php artisan check:links --skip-external

# Check only external links
php artisan check:links --skip-internal
```

## 🔄 Integration with CI/CD

### **GitHub Actions Example**

```yaml
name: Link Check
on: [push, pull_request]

jobs:
  link-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install
      - name: Run link check
        run: php artisan check:links --skip-external
```

### **Scheduled Checking**

Add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Run link check daily at 2 AM
    $schedule->command('check:links --export=csv')
             ->dailyAt('02:00')
             ->emailOutputOnFailure('admin@example.com');
}
```

## 🎯 Best Practices

### **Regular Monitoring**
- Run link checks after deployments
- Schedule daily checks for production sites
- Monitor external link health regularly

### **Performance Considerations**
- Use appropriate concurrency levels
- Set reasonable timeouts
- Consider rate limiting for external sites

### **Error Analysis**
- Export results for detailed analysis
- Focus on critical broken links first
- Monitor redirect chains for optimization

## 🆘 Troubleshooting

### **Command Not Found**
```bash
# Ensure command is registered
php artisan list | grep check:links

# Clear cache if needed
php artisan config:clear
php artisan cache:clear
```

### **Permission Issues**
```bash
# Ensure storage directory is writable
chmod -R 775 storage/
```

### **Memory Issues**
```bash
# Increase PHP memory limit
php -d memory_limit=512M artisan check:links
```

## 📝 License

This link checking system is part of the FlippDeal domain marketplace platform and follows the same licensing terms.

## 🤝 Contributing

To improve the link checker:

1. Add new link discovery methods
2. Enhance error reporting
3. Add support for more export formats
4. Improve performance optimizations
5. Add more comprehensive tests

---

**Happy Link Checking! 🔗✨**
