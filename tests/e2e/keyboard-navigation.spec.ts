import { expect, Locator, Page, test } from '@playwright/test';

async function tabUntilFocused(page: Page, target: Locator, maxTabs = 30): Promise<void> {
    for (let i = 0; i < maxTabs; i += 1) {
        await page.keyboard.press('Tab');
        const isFocused = await target.evaluate((el) => el === document.activeElement);
        if (isFocused) {
            return;
        }
    }

    throw new Error('Elemento nao recebeu foco apos navegar com Tab.');
}

test.describe('Acessibilidade por teclado - publico/auth', () => {
    test('home publica: foco chega nos CTAs principais', async ({ page }) => {
        await page.addInitScript(() => {
            window.localStorage.setItem('visitaai_cookies_aceitos', '1');
        });
        await page.goto('/');

        const entrar = page.locator('a[href$="/login"]');
        const consulta = page.locator('a[href$="/consulta-publica"]');

        await expect(entrar).toBeVisible();
        await expect(consulta).toBeVisible();

        await tabUntilFocused(page, entrar, 12);
        await tabUntilFocused(page, consulta, 6);
    });

    test('consulta publica: foco chega em voltar, campo e botao', async ({ page }) => {
        await page.addInitScript(() => {
            window.localStorage.setItem('visitaai_cookies_aceitos', '1');
        });
        await page.goto('/consulta-publica');

        const voltar = page.getByRole('link', { name: /Voltar para o in.*cio/i });
        const codigo = page.locator('#codigo');
        const consultar = page.locator('#consulta-codigo-btn');

        await expect(voltar).toBeVisible();
        await expect(codigo).toBeVisible();
        await expect(consultar).toBeVisible();

        await tabUntilFocused(page, voltar, 12);
        await tabUntilFocused(page, codigo, 6);
        await tabUntilFocused(page, consultar, 2);
    });

    test('login: foco inclui toggle de senha no ciclo de tab', async ({ page }) => {
        await page.goto('/login');

        const usuario = page.locator('#use_email');
        const senha = page.locator('#password');
        const toggleSenha = page.locator('#password-toggle-btn');
        const esqueci = page.locator('a[href$="/forgot-password"]');

        await expect(usuario).toBeVisible();
        await expect(senha).toBeVisible();
        await expect(toggleSenha).toBeVisible();

        await usuario.focus();
        await page.keyboard.press('Tab');
        await expect(senha).toBeFocused();

        await page.keyboard.press('Tab');
        await expect(toggleSenha).toBeFocused();

        await page.keyboard.press('Tab');
        await expect(esqueci).toBeFocused();
    });
});
