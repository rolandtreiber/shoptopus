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
        stage("Prune docker data") {
            steps {
                sh 'docker system prune -a --volumes -f'
            }
        }
        stage("Prune Docker Data") {
            steps {
                sh 'docker compose up -d --no-color --wait'
                sh 'docker compose ps'
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
