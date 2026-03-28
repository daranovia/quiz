node {

    checkout scm

    // Deploy env dev
    stage("Build") {
        docker.image('composer:2').inside('-u root') {
            sh 'rm -f composer.lock'
            sh 'composer install'
        }
    }

    // Testing
    stage("Testing") {
        docker.image('ubuntu:latest').inside('-u root') {
            sh 'echo "Ini adalah test"'
        }
    }

}
