#!/bin/bash

rm -f /var/run/minestore_frontend.pid
echo $!>/var/run/minestore_frontend.pid

restart_frontend(){
    source ~/.profile
    export NVM_DIR="$HOME/.nvm"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
    [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion
    export PNPM_HOME="/root/.local/share/pnpm"
      case ":$PATH:" in
      *":$PNPM_HOME:"*) ;;
      *) export PATH="$PNPM_HOME:$PATH" ;;
    esac
    source /root/.bashrc
    cd /var/www/minestore/frontend
    rm -rf .next .nuxt
    if pm2 describe minestore_frontend > /dev/null; then
        pm2 delete minestore_frontend
    fi

    pnpm install
    chown -R www-data:www-data /var/www/minestore/frontend
    pnpm run build
    pm2 start pnpm --name minestore_frontend -- run "$1" &
}
restart_frontend "start"

while true; do
    received_text=$(nc -l -p 25401)

    if [ "$received_text" = "restart" ]; then
        restart_frontend "start"
    fi

    if [ "$received_text" = "restart_dev" ]; then
        restart_frontend "dev"
    fi

    IFS='|' read -r -a parts <<< "$received_text"
    echo "$version"
    echo "$key"
    if [ "${parts[0]}" = "update" ]; then
        source ~/.profile
        export NVM_DIR="$HOME/.nvm"
        [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
        [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion
        export PNPM_HOME="/root/.local/share/pnpm"
          case ":$PATH:" in
          *":$PNPM_HOME:"*) ;;
          *) export PATH="$PNPM_HOME:$PATH" ;;
        esac
        export COMPOSER_ALLOW_SUPERUSER=1
        source /root/.bashrc
        cd /var/www/minestore
        version="${parts[1]}"
        key="${parts[2]}"
        bash <(curl -s https://minestorecms.com/api/getUpdates/"$version"/"$key")
    fi
done
