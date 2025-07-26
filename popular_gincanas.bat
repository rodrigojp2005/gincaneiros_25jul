@echo off
echo ================================
echo  POPULAR GINCANAS DE EXEMPLO
echo ================================
echo.
echo ATENCAO: Este comando vai adicionar gincanas de exemplo ao banco.
echo Isso NAO vai apagar suas gincanas existentes, apenas adicionar mais.
echo.
echo Gincanas atuais no banco:
php artisan tinker --execute="echo 'Total: ' . App\Models\Gincana::count() . ' gincanas' . PHP_EOL;"
echo.
set /p resposta="Deseja continuar e adicionar gincanas de exemplo? (s/n): "
if /i "%resposta%"=="s" (
    echo.
    echo Adicionando gincanas de exemplo...
    php artisan db:seed --class=GincanaSeeder
    echo.
    echo Gincanas apos a operacao:
    php artisan tinker --execute="echo 'Total: ' . App\Models\Gincana::count() . ' gincanas' . PHP_EOL;"
    echo.
    echo Sucesso! As gincanas de exemplo foram adicionadas.
    echo Agora o jogo tera mais variedade de locais!
) else (
    echo.
    echo Operacao cancelada. Nenhuma alteracao foi feita.
)
echo.
pause
