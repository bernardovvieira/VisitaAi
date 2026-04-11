import { defineConfig, devices } from '@playwright/test';
import { dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const PORT = Number(process.env.E2E_PORT || 8000);
const BASE_URL = process.env.E2E_BASE_URL || `http://127.0.0.1:${PORT}`;
const APP_DIR = dirname(fileURLToPath(import.meta.url));
const E2E_DB = `${APP_DIR}/database/e2e.sqlite`;
const E2E_ENV = [
    'APP_ENV=testing',
    'APP_DEBUG=true',
    'DB_CONNECTION=sqlite',
    `DB_DATABASE=${E2E_DB}`,
    'SESSION_DRIVER=file',
    'CACHE_STORE=array',
    'QUEUE_CONNECTION=sync',
    'MAIL_MAILER=log',
].join(' ');

export default defineConfig({
    testDir: './tests/e2e',
    timeout: 30_000,
    expect: {
        timeout: 10_000,
    },
    fullyParallel: false,
    reporter: [['list'], ['html', { open: 'never' }]],
    use: {
        baseURL: BASE_URL,
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    webServer: {
        command: `touch ${E2E_DB} && ${E2E_ENV} php ${APP_DIR}/artisan migrate --force && ${E2E_ENV} php ${APP_DIR}/artisan serve --host=127.0.0.1 --port=${PORT}`,
        url: `${BASE_URL}/login`,
        reuseExistingServer: true,
        timeout: 120_000,
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
