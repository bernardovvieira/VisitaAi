import { defineConfig, devices } from '@playwright/test';
import { dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const PORT = Number(process.env.E2E_PORT || 8000);
const BASE_URL = process.env.E2E_BASE_URL || `http://127.0.0.1:${PORT}`;
const APP_DIR = dirname(fileURLToPath(import.meta.url));
const E2E_DB = `${APP_DIR}/database/e2e.sqlite`;

/** Chave de teste (E2E/local). Mesmo valor em phpunit.xml (APP_KEY). Não usar em produção. */
const E2E_APP_KEY = 'base64:/eBG7nbmDvQwnBN7VNsU+4YJmvW8aYK5XzmGXTxJ3dg=';

const e2eWebServerEnv: NodeJS.ProcessEnv = {
    ...process.env,
    APP_ENV: 'testing',
    APP_DEBUG: 'true',
    APP_KEY: E2E_APP_KEY,
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: E2E_DB,
    SESSION_DRIVER: 'file',
    CACHE_STORE: 'array',
    QUEUE_CONNECTION: 'sync',
    MAIL_MAILER: 'log',
};

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
        command: `touch "${E2E_DB}" && php "${APP_DIR}/artisan" migrate --force && php "${APP_DIR}/artisan" serve --host=127.0.0.1 --port=${String(PORT)}`,
        env: e2eWebServerEnv,
        url: `${BASE_URL}/login`,
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
