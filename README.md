# ChunkStack

Laravel 12 导航网站项目。

## 技术栈

- **PHP 8.2+**
- **Laravel 12**
- **Pest** 测试框架
- **Laravel Pint** 代码格式化
- **Vite + Tailwind** 前端构建

## 快速开始

### 环境要求

- PHP 8.2+
- Composer
- Node.js & npm
- SQLite / MySQL / PostgreSQL

### 安装

```bash
# 安装 PHP 依赖
composer install

# 生成密钥
php artisan key:generate

# 运行迁移
php artisan migrate
```

### 开发

```bash
# 同时启动服务器、队列和 Vite
composer run dev

# 或分别启动
php artisan serve
npm run dev
php artisan queue:listen --tries=1
```

## 测试

```bash
# 运行所有测试
composer test
php artisan test

# 紧凑输出
php artisan test --compact

# 运行单个测试
php artisan test --filter=TestName
```

## 代码质量

```bash
# 格式化 PHP 代码
vendor/bin/pint --dirty
```

## 项目结构

```
app/
├── Console/Commands/     # 命令
├── Http/
│   ├── Controllers/      # 控制器
│   └── Middleware/      # 中间件
├── Models/              # 数据模型
└── Providers/           # 服务提供者

routes/
├── web.php             # Web 路由
└── console.php         # 控制台路由

tests/
├── Feature/            # 功能测试
└── Unit/               # 单元测试
```

## 许可证

MIT
