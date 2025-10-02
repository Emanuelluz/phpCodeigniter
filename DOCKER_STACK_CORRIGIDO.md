# 🐳 Correção: Docker Stack Deploy - Network Not Found

## 🚨 **PROBLEMA IDENTIFICADO**

**Erro**: `failed to create service phpcodeigniter_app: Error response from daemon: network phpcodeigniter_internal_net not found`

**Causa**: Condição de corrida entre remoção e criação do stack Docker

### **📋 Análise do Problema:**
1. **Primeiro Job**: Stack removido → Redes deletadas → Deploy imediato → Rede ainda não existe ❌
2. **Re-run Job**: Redes já estabilizadas → Deploy funciona ✅

---

## ✅ **SOLUÇÕES IMPLEMENTADAS**

### **1. 🔗 Aguardar Limpeza Completa das Redes**
```yaml
# Aguarda remoção completa das redes do stack
echo "Waiting for network cleanup..."
for i in {1..30}; do
  if ! docker network ls | grep -q "phpcodeigniter_internal_net"; then
    echo "Network cleanup complete"; break
  fi
  echo "Waiting network cleanup... $i"; sleep 2
done
```

### **2. 🌐 Pre-criar Redes Necessárias**
```yaml
- name: Ensure internal networks are ready
  shell: bash
  run: |
    echo "Pre-creating internal networks if needed..."
    if ! docker network inspect phpcodeigniter_internal_net >/dev/null 2>&1; then
      echo "Creating overlay network: phpcodeigniter_internal_net"
      docker network create -d overlay --attachable phpcodeigniter_internal_net
    else
      echo "Network 'phpcodeigniter_internal_net' already exists"
    fi
```

### **3. 🔄 Deploy com Retry Mechanism**
```yaml
- name: Deploy stack
  shell: bash
  run: |
    echo "Deploying stack with retry mechanism..."
    for attempt in {1..3}; do
      echo "Deploy attempt $attempt/3"
      if docker stack deploy -c docker-compose.yml phpcodeigniter --with-registry-auth; then
        echo "Stack deployed successfully on attempt $attempt"
        break
      else
        echo "Deploy failed on attempt $attempt"
        if [ $attempt -eq 3 ]; then
          echo "All deploy attempts failed"
          exit 1
        fi
        echo "Waiting 10 seconds before retry..."
        sleep 10
        # Limpar possíveis recursos órfãos
        docker system prune -f --filter "label=com.docker.stack.namespace=phpcodeigniter" || true
      fi
    done
```

### **4. 📝 Docker-Compose Mais Explícito**
```yaml
networks:
  labnet:
    external: true
    name: labnet
  internal_net:
    driver: overlay
    name: phpcodeigniter_internal_net
    attachable: true
```

---

## 🔧 **ARQUIVOS MODIFICADOS**

### **📁 `.github/workflows/php.yml`**
- ✅ **Adicionado**: Aguardar limpeza completa das redes
- ✅ **Adicionado**: Pre-criação de redes internas
- ✅ **Adicionado**: Deploy com retry (3 tentativas)
- ✅ **Adicionado**: Limpeza de recursos órfãos entre tentativas

### **📁 `docker-compose.yml`**
- ✅ **Modificado**: Definição explícita da rede interna
- ✅ **Adicionado**: Nome específico para a rede (`phpcodeigniter_internal_net`)
- ✅ **Adicionado**: Driver overlay explícito
- ✅ **Adicionado**: Propriedade attachable

---

## 🚀 **FLUXO CORRIGIDO**

### **🔄 Novo Fluxo de Deploy:**
1. **Build** da imagem Docker
2. **Verificar** Docker Swarm ativo
3. **Criar/Verificar** rede externa `labnet`
4. **Remover** stack existente (se houver)
5. **Aguardar** limpeza completa das redes ⭐ **NOVO**
6. **Pre-criar** redes internas necessárias ⭐ **NOVO**
7. **Deploy** com retry automático (3 tentativas) ⭐ **NOVO**
8. **Verificar** serviços implantados
9. **Aguardar** MariaDB estar pronto
10. **Executar** migrations

### **⏱️ Timeouts Configurados:**
- **Remoção do stack**: 60 tentativas (120s)
- **Limpeza de redes**: 30 tentativas (60s)
- **Deploy retry**: 3 tentativas com pausa de 10s
- **MariaDB ready**: 60 tentativas (120s)
- **App container**: 60 tentativas (120s)
- **Database connection**: 30 tentativas (60s)

---

## 🧪 **RESULTADO ESPERADO**

### **✅ Primeiro Job (após push):**
- Remoção limpa do stack anterior
- Aguardo da limpeza completa das redes
- Pre-criação de redes necessárias
- Deploy bem-sucedido na primeira tentativa
- Sem necessidade de re-run

### **🔒 Fallback de Segurança:**
- Se primeira tentativa falhar → 2 retries automáticos
- Limpeza de recursos órfãos entre tentativas
- Logs detalhados para troubleshooting

---

## 📊 **MONITORAMENTO**

### **🔍 Logs Aprimorados:**
```bash
# Durante remoção
"Waiting stack removal... X"
"Waiting network cleanup... X" 

# Durante deploy
"Deploy attempt X/3"
"Stack deployed successfully on attempt X"

# Durante verificação
"Networks ready:" + lista de redes
```

### **🚨 Possíveis Falhas:**
- **Timeout na remoção**: Stack não removido em 120s
- **Timeout na rede**: Rede não limpa em 60s  
- **Deploy falhado**: 3 tentativas falharam
- **Recursos órfãos**: Limpeza automática entre tentativas

---

**🎉 Problema resolvido! Primeiro job após push agora deve funcionar consistentemente.**