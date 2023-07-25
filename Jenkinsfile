pipeline {
    agent any
    stages {
        stage("Verify tooling") {
            steps {
                sh '''
                    docker info
                    docker version
                    docker compose version
                '''
            }
        }
        stage("Clear all running docker containers") {
            steps {
                script {
                    try {
                        sh 'docker rm -f $(docker ps -a -q)'
                    } catch (Exception e) {
                        echo 'No running container to clear up...'
                    }
                }
            }
        }
        stage("Copy .env file") {
            steps {
                sh 'cp ./.env.pipeline ./.env'
                sh 'cat ./.env'
            }
        }
        stage("Start Docker") {
            steps {
                sh 'make up'
                sh 'docker compose ps'
                sh 'docker ps'
                sleep(time:30, unit: 'SECONDS')
                sh 'docker ps'
            }
        }
        stage("Run Composer Install") {
            steps {
                sh 'docker compose run --rm sh-composer install --ignore-platform-reqs --no-interaction'
            }
        }
        stage("Run Tests") {
            steps {
                sh 'docker compose run --rm sh-artisan test'
            }
        }
    }
    post {
        always {
            sh 'docker compose down --remove-orphans -v'
            sh 'docker compose ps'
        }
    }
}
