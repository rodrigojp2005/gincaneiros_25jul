@echo off
echo ===== LIMPEZA DO PROJETO LARAVEL PARA O GITHUB =====

:: Baixar o .gitignore padrão do Laravel
echo Baixando .gitignore do Laravel...
curl -o .gitignore https://raw.githubusercontent.com/github/gitignore/main/Laravel.gitignore

:: Remover o .env do controle de versão (mantendo no disco)
echo Removendo .env do Git (mas mantendo no projeto local)...
git rm --cached .env

:: Criar um .env.example (caso ainda não exista)
if not exist ".env.example" (
    echo Criando .env.example...
    copy .env .env.example > nul
    echo Lembre-se de limpar dados sensíveis do .env.example!
) else (
    echo .env.example já existe — pulei essa parte.
)

:: Adicionar e comitar as alterações
echo Adicionando arquivos ao Git...
git add .

echo Fazendo commit...
git commit -m "Adiciona .gitignore, remove .env e cria .env.example"

:: Push para o GitHub
echo Enviando alterações para o GitHub...
git push

echo ===== TUDO PRONTO, RODRIGO! =====
pause