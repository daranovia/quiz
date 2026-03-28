pipeline {
    agent any

    environment {
        COMPOSER_HOME = "${WORKSPACE}/.composer"
        DEPLOY_USER = "dara"
        DEPLOY_HOST = "192.168.0.108"                
        APP_DIR = "/var/www/dev"
        GIT_REPO = "git@github.com:daranovia/quiz.git"
        GIT_BRANCH = "main"
    }

    stages {

        stage('Checkout') {
            steps {
                sshagent(['jenkins-ssh-key']) {
                    sh """
                        if [ ! -d src ]; then
                            git clone -b $GIT_BRANCH $GIT_REPO src
                        else
                            cd src
                            git fetch origin
                            git reset --hard origin/$GIT_BRANCH
                        fi

                        git config --global --add safe.directory ${WORKSPACE}
                        git config --global --add safe.directory ${WORKSPACE}/src
                    """
                }
            }
        }

        stage('Build Composer') {
            steps {
                script {
                    docker.image('composer:2').inside('--entrypoint=""') {
                        sh """
                            cd src
                            composer install --no-dev --optimize-autoloader
                            php artisan package:discover --ansi
                        """
                    }
                }
            }
        }

        stage('Testing') {
            steps {
                echo 'Testing stage'
            }
        }

        stage('Deploy to Debian') {
            steps {
                sshagent(['jenkins-ssh-key']) {
                    sh """
                        # Buat folder Laravel di server kalau belum ada
                        ssh -o StrictHostKeyChecking=no $DEPLOY_USER@$DEPLOY_HOST "mkdir -p $APP_DIR"

                        # Copy semua file Laravel ke server
                        scp -o StrictHostKeyChecking=no -r ${WORKSPACE}/src/* $DEPLOY_USER@$DEPLOY_HOST:$APP_DIR/

                        # Jalankan composer & migrate di server
                        ssh -o StrictHostKeyChecking=no $DEPLOY_USER@$DEPLOY_HOST '
                            cd $APP_DIR
                            composer install --no-dev --optimize-autoloader
                            php artisan migrate --force
                        '
                    """
                }
            }
        }

    }

    post {
        success {
            echo 'Laravel berhasil di-deploy ke server!'
        }
        failure {
            echo 'Deploy gagal. Cek console log di Jenkins.'
        }
    }
}
