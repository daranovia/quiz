pipeline {
    agent any

    environment {
        APP_DIR      = "/var/jenkins_home/workspace/laravel-devops/src"
        DEPLOY_USER  = "dara"
        DEPLOY_HOST  = "10.17.37.137"
        DEPLOY_DIR   = "/var/www/dev"
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
                    sh "composer install --no-dev --optimize-autoloader --working-dir=${APP_DIR}"
                }
            }
        }

        stage('Testing') {
            steps {
                script {
                    sh "cd ${APP_DIR} && vendor/bin/phpunit"
                }
            }
        }

        stage('Deploy to Debian') {
            steps {
                sshagent(['jenkins-ssh']) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} '
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
                    """
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
