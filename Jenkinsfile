pipeline {
    agent any

    environment {
        APP_DIR = "/var/jenkins_home/workspace/laravel-devops/src"
        DEPLOY_USER = "dara"
        DEPLOY_HOST = "10.17.37.137"
        DEPLOY_DIR  = "/var/www/dev"
        COMPOSER_HOME = "${WORKSPACE}/.composer"
        DOCKER_HOST = ""
    }

    stages {

        stage('Checkout SCM') {
            steps {
                checkout([
                    $class: 'GitSCM',
                    branches: [[name: '*/main']],
                    doGenerateSubmoduleConfigurations: false,
                    extensions: [],
                    userRemoteConfigs: [[
                        url: 'git@github.com:daranovia/quiz.git',
                        credentialsId: 'jenkins-ssh'
                    ]]
                ])
            }
        }

        stage('Build Composer') {
            steps {
                script {
                    sh(script: "mkdir -p ${APP_DIR}")

                    // Jalankan composer via Docker tanpa harus escape $
                    sh(script: '''
                        docker run --rm -u $(id -u):$(id -g) \
                            -v ${APP_DIR}:/app \
                            -v ${COMPOSER_HOME}:/composer \
                            -w /app \
                            composer:2 \
                            composer install --no-dev --optimize-autoloader
                    ''')
                }
            }
        }

        stage('Testing') {
            steps {
                script {
                    sh(script: '''
                        docker run --rm -u $(id -u):$(id -g) \
                            -v ${APP_DIR}:/app \
                            -w /app \
                            php:8.2-cli \
                            bash -c "composer install --no-dev --optimize-autoloader && vendor/bin/phpunit"
                    ''')
                }
            }
        }

        stage('Deploy to Debian') {
            steps {
                sshagent(['jenkins-ssh']) {
                    sh(script: '''
                        ssh -o StrictHostKeyChecking=no -T ${DEPLOY_USER}@${DEPLOY_HOST} '
                            cd ${DEPLOY_DIR} || exit
                            git reset --hard
                            git pull origin main
                            composer install --no-dev --optimize-autoloader
                            php artisan migrate --force
                            php artisan db:seed --force
                            php artisan cache:clear
                            php artisan view:clear
                            php artisan config:clear
                            php artisan route:clear
                        '
                    ''')
                }
            }
        }
    }

    post {
        success {
            echo "Pipeline selesai dengan sukses"
        }
        failure {
            echo "Pipeline gagal"
        }
    }
}
