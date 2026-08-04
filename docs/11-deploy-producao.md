# Etapa 11 — Deployment em produção (DigitalOcean VPS)

Sistema de Gestão de Feira Gastronómica e Cultural Escolar (Laravel 12)

Guia passo a passo para publicar o sistema numa Droplet da DigitalOcean já criada (Ubuntu 22.04/24.04), acessível por IP (sem domínio, por agora). Os blocos marcados **"no servidor"** correm por SSH na Droplet; os marcados **"no seu PC"** correm no XAMPP/Windows onde o projeto vive hoje. Os ficheiros de configuração referidos aqui (`nginx-feiraemacao.conf`, `feiraemacao-queue.service`, `.env.production.example`, `deploy.sh`) estão em `deploy/`, prontos a copiar.

---

## 0. Antes de começar: o projeto precisa de estar num repositório Git

Hoje o projeto não é um repositório Git — sem isso, não há forma sã de enviar o código para o servidor nem de atualizar depois. Duas opções:

**Opção A — Git (recomendado, faz toda a diferença nas próximas atualizações)**

No seu PC:
```
git init
git add .
git commit -m "Primeira versão para deployment"
```
Crie um repositório **privado** no GitHub (ou GitLab) e envie:
```
git remote add origin https://github.com/SEU_UTILIZADOR/feiraemacao.git
git branch -M main
git push -u origin main
```

**Opção B — Sem Git, envio direto**

Se preferir não usar Git agora, pode enviar os ficheiros diretamente por `scp`/SFTP (ex.: WinSCP) para `/var/www/feiraemacao` no servidor, saltando os passos de `git clone`/`git pull` abaixo. **Atualizações futuras** exigem repetir o envio manual do zero — funciona, mas dá mais trabalho a cada mudança. O resto deste guia assume a Opção A.

---

## 1. Primeiro acesso e preparação da Droplet

**No servidor** (`ssh root@SEU_IP`):

```bash
apt update && apt upgrade -y

# Utilizador próprio em vez de trabalhar como root diretamente
adduser feiraemacao
usermod -aG sudo feiraemacao

# Firewall básico
ufw allow OpenSSH
ufw enable
```

A partir daqui, ligue-se com `ssh feiraemacao@SEU_IP` para o resto do guia.

---

## 2. Instalar PHP 8.3, MySQL, Nginx, Composer e Node

**No servidor:**

```bash
# PHP 8.3 (satisfaz "php": "^8.2" do composer.json) + extensões que o Laravel/dompdf/QR code precisam
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl

# MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Nginx
sudo apt install -y nginx
sudo ufw allow 'Nginx Full'

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 20 (para compilar o Bootstrap/CSS/JS via Vite)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# git (se ainda não vier instalado)
sudo apt install -y git
```

---

## 3. Criar a base de dados

**No servidor:**

```bash
sudo mysql
```
Dentro do `mysql`:
```sql
CREATE DATABASE feiraemacao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'feiraemacao'@'localhost' IDENTIFIED BY 'TROCAR_POR_UMA_SENHA_FORTE';
GRANT ALL PRIVILEGES ON feiraemacao.* TO 'feiraemacao'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```
Guarde essa senha — vai para o `.env` no passo 5.

---

## 4. Clonar o projeto

**No servidor:**

```bash
sudo mkdir -p /var/www/feiraemacao
sudo chown feiraemacao:feiraemacao /var/www/feiraemacao
git clone https://github.com/SEU_UTILIZADOR/feiraemacao.git /var/www/feiraemacao
cd /var/www/feiraemacao
```

---

## 5. Configurar o `.env` de produção

**No servidor**, a partir do modelo já preparado:
```bash
cp deploy/.env.production.example .env
nano .env
```
Preencha pelo menos:
- `APP_URL=http://SEU_IP_AQUI`
- `DB_PASSWORD` — a senha que definiu no passo 3
- `MAIL_*` — se já tiver credenciais de email; senão deixe `MAIL_MAILER=log` por agora (as notificações ficam só gravadas no log, não chegam a ninguém — corrigir antes de usar a sério)

---

## 6. Instalar dependências, compilar assets e preparar a aplicação

**No servidor:**

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan key:generate --force
php artisan storage:link
php artisan migrate --force --seed
```

O `--seed` cria os 4 papéis e a conta de arranque `admin@feiraemacao.local` / `MudarNo1Acesso!`. **Troque essa senha assim que entrar pela primeira vez** — é a mesma regra que já se aplica em desenvolvimento local.

Permissões (o servidor web precisa de escrever em `storage/` e `bootstrap/cache/`):
```bash
sudo chown -R feiraemacao:www-data /var/www/feiraemacao
sudo chmod -R 775 storage bootstrap/cache
```

---

## 7. Configurar o Nginx

**No servidor:**

```bash
sudo cp deploy/nginx-feiraemacao.conf /etc/nginx/sites-available/feiraemacao
sudo nano /etc/nginx/sites-available/feiraemacao   # trocar SEU_IP_AQUI pelo IP real da Droplet
sudo ln -s /etc/nginx/sites-available/feiraemacao /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

---

## 8. Fila de processamento (relatórios PDF/CSV, notificações)

Sem isto, os Relatórios (Painel → Relatórios) ficam presos em "processando" para sempre — é o mesmo `queue:work` que já usa manualmente em desenvolvimento, agora a correr sempre em segundo plano via `systemd`.

**No servidor:**

```bash
sudo cp deploy/feiraemacao-queue.service /etc/systemd/system/feiraemacao-queue.service
sudo systemctl daemon-reload
sudo systemctl enable --now feiraemacao-queue
sudo systemctl status feiraemacao-queue   # confirmar "active (running)"
```

---

## 9. Testar

No browser: `http://SEU_IP_AQUI`. Deve ver a página pública. Entre em `/login` com a conta de arranque, troque a senha, e crie a primeira edição da feira.

---

## 10. Checklist de segurança antes de usar a sério

- [ ] Senha de `admin@feiraemacao.local` trocada.
- [ ] `.env`: `APP_DEBUG=false`, `APP_ENV=production` (já no modelo — confirme que não voltou a `true`).
- [ ] `MAIL_MAILER` configurado com um driver real (não `log`), senão ninguém recebe emails de aprovação/rejeição.
- [ ] `feiraemacao-queue` a correr (`systemctl status feiraemacao-queue`).
- [ ] Firewall ativo (`sudo ufw status`) — só SSH e Nginx Full abertos.
- [ ] Backup da base de dados agendado (nem que seja um `mysqldump` diário via cron para já).

---

## 11. Atualizar o sistema no futuro

Depois desta primeira instalação, cada atualização é:
```bash
cd /var/www/feiraemacao
./deploy/deploy.sh
```
Isto faz `git pull`, reinstala dependências, recompila os assets, corre migrations pendentes, limpa caches e reinicia a fila — sem precisar de repetir os passos 1–8. Certifique-se de que `deploy/deploy.sh` tem permissão de execução (`chmod +x deploy/deploy.sh`, uma vez só).

---

## 12. Quando tiver um domínio

Quando apontar um domínio (ex.: `feiraemacao.co.mz`) ao IP da Droplet (registo DNS tipo `A`):
1. Trocar `server_name SEU_IP_AQUI;` no `nginx-feiraemacao.conf` pelo domínio.
2. Trocar `APP_URL` no `.env` para `https://feiraemacao.co.mz`.
3. Instalar HTTPS grátis:
   ```bash
   sudo apt install -y certbot python3-certbot-nginx
   sudo certbot --nginx -d feiraemacao.co.mz
   ```
   O Certbot ajusta o Nginx automaticamente e renova o certificado sozinho.
