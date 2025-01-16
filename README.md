
# Chat em Tempo-Real - Laravel Reverb

Esse projeto implementa o Laravel Reverb como serviço de WebSockets para que chat's possam ser em real-time.




## Stack utilizada

**Front-end:** Blade, Tailwind CSS, Laravel Livewire

**Back-end:** Laravel Framework


## Instalação

### Recomendado usar o Laravel Herd

Rode `git clone https://github.com/anderson-jorge-cruz/chat-laravel-reverb.git` para clonar o repositório


```bash
  cd chat-laravel-reverb
  composer install
  npm install && npm run build
```

Set-up Env-file
```
  copy .env.example .env
  php artisan key:generate
  php artisan install:broadcasting
```

Configure o Laravel-Reverb .env credentials
```
  REVERB_APP_ID= e.g.: 123456
  REVERB_APP_KEY= e.g.: 76qZyQxw18
  REVERB_APP_SECRET= e.g: rgQFFCszUE
```

Execute as migrações de banco de dados:
```
  php artisan migrate
```

Popular o banco de dados
```
  php artisan db:seed
```

Execute o serviço Laravel Reverb
```
  `php artisan reverb:start`
```


## Funcionalidades

- Ciclo completo de autenticação
- Chat entre usuários
- Atualização em tempo real



## Aprendizados

Através desse projeto foi possível compreender sobre WebSockets, como funcionam e quando faz sentido usá-los.



## Rodando os testes

Para rodar os testes, rode o seguinte comando

```bash
  php artisan test
```

