pipeline {
    agent any

    environment {
        APP_DIR = "/var/jenkins_home/workspace/laravel-devops/src"
        DEPLOY_USER = "dara"
        DEPLOY_HOST = "192.168.0.108"
        DEPLOY_DIR  = "/var/www/dev"
        COMPOSER_HOME = "${WORKSPACE}/.composer"
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
                    sh "mkdir -p ${APP_DIR}"

                    docker.withServer('unix:///var/run/docker.sock') {
                        docker.image('composer:2').inside("-u 1000:1000 -v ${APP_DIR}:${APP_DIR}") {
                            sh """
                                cd ${APP_DIR}
                                composer install --no-dev --optimize-autoloader
                            """
                        }
                    }
                }
            }
        }

        stage('Testing') {
            steps {
                echo "Running tests (placeholder)"
            }
        }

        stage('Deploy to Debian') {
            steps {
                sshagent(['jenkins-ssh']) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} '
                            cd ${DEPLOY_DIR}
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
                    """
                }
            }
        }
    }

    post {
        success {
            echo "Pipeline selesai dengan sukses 🚀"
        }
        failure {
            echo "Pipeline gagal ❌"
        }
    }
}
