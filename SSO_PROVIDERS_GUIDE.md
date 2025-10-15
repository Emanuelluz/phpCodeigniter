# 📚 Guia Rápido de Configuração dos Providers SSO

---

## 🔐 1. Provider Local (Padrão)

### **Configuração Automática**
O provider local já vem configurado por padrão via seeder.

### **Dados Padrão:**
```json
{
    "name": "Local Authentication",
    "type": "local",
    "is_enabled": true
}
```

### **Uso:**
```php
// Autenticação direta com username/password
POST /sso/authenticate
{
    "provider": "local",
    "username": "usuario",
    "password": "senha"
}
```

---

## 🏢 2. Provider LDAP/Active Directory

### **Criar Provider via Interface:**
1. Acesse `/sso/admin/providers/create`
2. Selecione tipo: **LDAP**
3. Preencha configurações:

```json
{
    "host": "ldap.empresa.com",
    "port": 389,
    "base_dn": "DC=empresa,DC=com",
    "bind_dn": "CN=admin,DC=empresa,DC=com",
    "bind_password": "senha_admin",
    "user_filter": "(sAMAccountName={username})",
    "use_ssl": false,
    "use_tls": true,
    "attributes": ["dn", "cn", "mail", "memberof", "displayname"],
    "attribute_mapping": {
        "username": "samaccountname",
        "email": "mail",
        "name": "displayname",
        "groups": "memberof"
    }
}
```

### **Configuração via .env:**
```env
LDAP_HOST=ldap.empresa.com
LDAP_PORT=389
LDAP_BASE_DN=DC=empresa,DC=com
LDAP_BIND_DN=CN=admin,DC=empresa,DC=com
LDAP_BIND_PASSWORD=senha_secreta
LDAP_USER_FILTER=(sAMAccountName={username})
LDAP_USE_SSL=false
LDAP_USE_TLS=true
```

### **Testar Conexão:**
```php
POST /sso/admin/test/ldap
{
    "provider_id": 2
}

// Resposta:
{
    "success": true,
    "message": "Conexão LDAP estabelecida com sucesso",
    "details": {
        "host": "ldap.empresa.com",
        "port": 389,
        "base_dn": "DC=empresa,DC=com"
    }
}
```

### **Uso:**
```php
// Autenticação LDAP
POST /sso/authenticate
{
    "provider": "ldap",
    "username": "joao.silva",
    "password": "senha123"
}
```

### **Sincronizar Usuários:**
```php
GET /sso/admin/users/sync

// Retorna:
{
    "success": true,
    "users_found": 150,
    "users_synced": 150
}
```

---

## 🌐 3. Provider OAuth 2.0

### **3.1 Google OAuth**

#### **Pré-requisitos:**
1. Criar projeto no [Google Cloud Console](https://console.cloud.google.com)
2. Habilitar Google+ API
3. Criar credenciais OAuth 2.0
4. Adicionar redirect URI: `http://localhost:8080/sso/callback/oauth`

#### **Configuração:**
```json
{
    "oauth_provider": "google",
    "client_id": "123456789.apps.googleusercontent.com",
    "client_secret": "GOCSPX-xxxxxxxxxxxxxxxxx",
    "redirect_uri": "http://localhost:8080/sso/callback/oauth",
    "scope": "openid email profile"
}
```

#### **Variáveis .env:**
```env
OAUTH_GOOGLE_CLIENT_ID=123456789.apps.googleusercontent.com
OAUTH_GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxx
OAUTH_GOOGLE_REDIRECT_URI=http://localhost:8080/sso/callback/oauth
```

#### **Criar Provider:**
```sql
INSERT INTO sso_providers (name, type, config, is_enabled) VALUES (
    'Google Login',
    'oauth',
    '{"oauth_provider":"google","client_id":"xxx","client_secret":"xxx","redirect_uri":"http://localhost:8080/sso/callback/oauth"}',
    1
);
```

#### **Fluxo de Autenticação:**
```php
// 1. Usuário clica em "Login com Google"
GET /sso/login?provider=google

// 2. Redireciona para Google (automaticamente)
https://accounts.google.com/o/oauth2/v2/auth?
    client_id=xxx&
    redirect_uri=xxx&
    response_type=code&
    scope=openid+email+profile&
    state=random_string

// 3. Usuário autoriza no Google

// 4. Google redireciona de volta
GET /sso/callback/oauth?code=xxx&state=xxx

// 5. Sistema processa e autentica
```

---

### **3.2 Microsoft/Azure AD**

#### **Dependência Adicional:**
```bash
composer require thenetworg/oauth2-azure
```

#### **Configuração:**
```json
{
    "oauth_provider": "microsoft",
    "client_id": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
    "client_secret": "xxx~xxxxxxxxxxxxxxxxxxxxxxxxx",
    "redirect_uri": "http://localhost:8080/sso/callback/oauth",
    "tenant": "common",
    "scope": "openid email profile User.Read"
}
```

#### **Portal Azure:**
1. Acesse [Azure Portal](https://portal.azure.com)
2. App Registrations → New Registration
3. Redirect URI: `http://localhost:8080/sso/callback/oauth`
4. Certificates & secrets → New client secret
5. API permissions → Add Microsoft Graph → User.Read

---

### **3.3 GitHub**

#### **Dependência Adicional:**
```bash
composer require league/oauth2-github
```

#### **Configuração:**
```json
{
    "oauth_provider": "github",
    "client_id": "Iv1.xxxxxxxxxxxxxxxx",
    "client_secret": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "redirect_uri": "http://localhost:8080/sso/callback/oauth",
    "scope": "user:email"
}
```

#### **GitHub Settings:**
1. Settings → Developer settings → OAuth Apps
2. New OAuth App
3. Callback URL: `http://localhost:8080/sso/callback/oauth`

---

## 🔐 4. Provider SAML 2.0

### **Configuração Completa:**
```json
{
    "sp_entity_id": "http://localhost:8080",
    "acs_url": "http://localhost:8080/sso/saml/acs",
    "sls_url": "http://localhost:8080/sso/saml/sls",
    "sp_certificate": "-----BEGIN CERTIFICATE-----\nMIIDXTCCAkWgAwIBAgIJAKZ...\n-----END CERTIFICATE-----",
    "sp_private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0B...\n-----END PRIVATE KEY-----",
    
    "idp_entity_id": "https://idp.empresa.com",
    "idp_sso_url": "https://idp.empresa.com/saml/sso",
    "idp_slo_url": "https://idp.empresa.com/saml/slo",
    "idp_certificate": "-----BEGIN CERTIFICATE-----\nMIIDdDCCAlygAwIBAgIGAXo...\n-----END CERTIFICATE-----",
    
    "attribute_mapping": {
        "username": "uid",
        "email": "mail",
        "name": "displayName",
        "groups": "memberOf"
    },
    
    "nameid_format": "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress",
    "sign_authn_request": false,
    "want_assertions_signed": true
}
```

### **Gerar Certificados SP (Self-Signed):**
```bash
# Gerar chave privada
openssl genrsa -out saml_sp.key 2048

# Gerar certificado (válido por 10 anos)
openssl req -new -x509 -key saml_sp.key -out saml_sp.crt -days 3650

# Converter para formato inline (sem quebras de linha)
cat saml_sp.key | tr -d '\n'
cat saml_sp.crt | tr -d '\n'
```

### **Obter Metadata SP:**
```php
GET /sso/admin/providers/metadata/4

// Retorna XML:
<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="http://localhost:8080">
    <md:SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="http://localhost:8080/sso/saml/acs" index="1"/>
        <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="http://localhost:8080/sso/saml/sls"/>
    </md:SPSSODescriptor>
</md:EntityDescriptor>
```

### **Configurar no IdP:**
1. Acesse seu Identity Provider (Okta, OneLogin, AD FS, etc)
2. Criar nova aplicação SAML
3. Upload metadata SP ou configurar manualmente:
   - ACS URL: `http://localhost:8080/sso/saml/acs`
   - Entity ID: `http://localhost:8080`
   - SLO URL: `http://localhost:8080/sso/saml/sls`
4. Baixar certificado IdP
5. Copiar URLs de SSO e SLO

### **Fluxo SAML:**
```php
// 1. Usuário clica em "Login com SAML"
GET /sso/login?provider=saml

// 2. Redireciona para IdP com SAML Request
POST https://idp.empresa.com/saml/sso
SAMLRequest=<base64_encoded_xml>

// 3. Usuário autentica no IdP

// 4. IdP redireciona de volta com SAML Response
POST /sso/saml/acs
SAMLResponse=<base64_encoded_xml>

// 5. Sistema valida asserção e autentica
```

---

## 🧪 Testes de Providers

### **Script de Teste Completo:**
```bash
#!/bin/bash

echo "=== Testando Providers SSO ==="

# 1. Local
echo "1. Testando Local..."
curl -X POST http://localhost:8080/sso/authenticate \
  -d "provider=local&username=admin&password=admin123"

# 2. LDAP
echo "2. Testando LDAP..."
curl -X POST http://localhost:8080/sso/admin/test/ldap \
  -d "provider_id=2"

# 3. OAuth (testar configuração)
echo "3. Testando OAuth..."
curl -X POST http://localhost:8080/sso/admin/test/oauth \
  -d "provider_id=3"

# 4. SAML (testar configuração)
echo "4. Testando SAML..."
curl -X POST http://localhost:8080/sso/admin/test/saml \
  -d "provider_id=4"
```

---

## 📊 Comparação de Providers

| Feature | Local | LDAP | OAuth | SAML |
|---------|-------|------|-------|------|
| **Facilidade** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| **Segurança** | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Enterprise** | ❌ | ✅ | ✅ | ✅ |
| **Single Sign-On** | ❌ | ❌ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Single Logout** | ✅ | ✅ | ❌ | ✅ |
| **Sincronização** | ❌ | ✅ | ❌ | ❌ |
| **Dependências** | Nenhuma | PHP LDAP | Composer | Composer |
| **Casos de Uso** | Desenvolvimento, apps simples | Active Directory, OpenLDAP | Google, Microsoft, GitHub | Enterprise SSO, compliance |

---

## 🔧 Troubleshooting Comum

### **LDAP: "Connection refused"**
```bash
# Verificar conectividade
telnet ldap.empresa.com 389

# Testar bind
ldapsearch -x -H ldap://ldap.empresa.com -D "CN=admin,DC=empresa,DC=com" -w senha -b "DC=empresa,DC=com"
```

### **OAuth: "Invalid redirect_uri"**
- Verificar se a URI está EXATAMENTE igual no provider (Google Console, etc)
- Incluir protocolo: `http://` ou `https://`
- Não usar trailing slash

### **SAML: "Invalid signature"**
- Verificar se certificado IdP está correto
- Verificar se certificado SP está correto
- Sincronizar relógios (NTP) entre SP e IdP
- Habilitar debug: `SSO_DEBUG_MODE=true`

### **Rate Limit: "Too many attempts"**
```php
// Limpar rate limit manualmente
use Modules\Sso\Filters\RateLimitFilter;

RateLimitFilter::clearRateLimit('192.168.1.100', 'usuario');
```

---

## 📚 Recursos Externos

### **LDAP:**
- [PHP LDAP Extension](https://www.php.net/manual/en/book.ldap.php)
- [Active Directory Attributes](https://docs.microsoft.com/en-us/windows/win32/adschema/attributes-all)

### **OAuth:**
- [Google OAuth Setup](https://developers.google.com/identity/protocols/oauth2)
- [Microsoft OAuth](https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-auth-code-flow)
- [GitHub OAuth](https://docs.github.com/en/developers/apps/building-oauth-apps)

### **SAML:**
- [SAML 2.0 Basics](https://www.samltool.com/generic_sso_req.php)
- [OneLogin SAML Toolkits](https://developers.onelogin.com/saml)
- [Okta SAML Guide](https://developer.okta.com/docs/concepts/saml/)

---

**Última atualização: 15/10/2025**
