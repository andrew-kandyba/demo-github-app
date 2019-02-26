# Demo Github App
Пример простого ресивера гитхаб вебхуков.

## Установка
`composer install`

`php app.php`

## Как это работает?
Приложение представляет собой http сервер, который прокидывает request через цепочку посредиков (middleware),
каждый из которых отвечает за определенный (и только один) тип ивента (вебхук), как только посредник находит "свое" исполняется комманда-обработчик.

Пример обработки push ивента:

```
$server = new Server([
    // Зарегистрировали посредника для работы с push вебхуком
    new Push($authService, $commandFactory),
    ....
]);
```
Посредник
```
class Push extends AbstractEvent
{
    ...
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if (self::GITHUB_WEBHOOK_PUSH === $this->getWebhookType($request)) {
            $payload = json_decode($request->getBody()->getContents());
            //На основе installationId получили access токен для нашего приложения (понадобится при выполнении комманды)
            $installationAccessToken = $this->authService->getInstallationAccessToken($payload->installation->id);

            // Cоздаем и исполняем комманду на пуллинг репозитория в котором появились изменения.
            $this->commandFactory->create('pull', $installationAccessToken, $payload->repository->full_name)
                                 ->run();
        }
        
        //Передаем реквест следующему посреднику.
        return $next($request);
    }
    
    ...
```
Комманда:
```
class Pull extends AbstractCommand
{
   ...
    public function run(): void
    {
        $repositoryPull = sprintf(
            'git pull https://x-access-token:%s@github.com/%s.git',
            $this->installationAccessToken,
            $this->repoFullName
        );

        $childProcess = new Process($repositoryPull, $this->workingDir);
        $childProcess->start($this->loop);

        $childProcess->on('exit', function ($exitCode, $termSignal) use ($childProcess) {
            if (null !== $termSignal) {
                $childProcess->close();
            }
        });
    }
    ...
```

## Возможности
В качестве примеров реализована работа с `installation (created + deleted actions)` и `push` вебхуками, а именно:
- При получении `installation (created)` (пользователь установил приложение) будет выполнено клонирование репозиториев (к котрым есть доступ) в директорию `resources`.
- При получении `installation (deleted)` (пользователь удалил приложение) директория `resources` будет очищенна.
- При получении `push` для обновления данных будет выполнена комманда `pull` в локальной копии репозитория.
 
## Лицензия
MIT
