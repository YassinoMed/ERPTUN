pipeline {
  agent any
  environment {
    REGISTRY = "registry.example.com"
    IMAGE_NAME = "erpgo/app"
    IMAGE_TAG = "${env.BUILD_NUMBER}"
    KUBE_NAMESPACE = "erpgo"
    DOCKER_CREDENTIALS_ID = "docker-registry"
    KUBECONFIG_CREDENTIALS_ID = "kubeconfig"
  }
  stages {
    stage('Checkout') {
      steps {
        checkout scm
      }
    }
    stage('Build') {
      steps {
        sh "docker build -f Dockerfile.k8s -t ${REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG} ."
      }
    }
    stage('Push') {
      steps {
        withCredentials([usernamePassword(credentialsId: "${DOCKER_CREDENTIALS_ID}", usernameVariable: 'REG_USER', passwordVariable: 'REG_PASS')]) {
          sh "echo ${REG_PASS} | docker login ${REGISTRY} -u ${REG_USER} --password-stdin"
          sh "docker push ${REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG}"
        }
      }
    }
    stage('Deploy') {
      steps {
        withCredentials([file(credentialsId: "${KUBECONFIG_CREDENTIALS_ID}", variable: 'KUBECONFIG_FILE')]) {
          sh "kubectl apply -f k8s/k8s.yaml --kubeconfig ${KUBECONFIG_FILE}"
          sh "kubectl set image deployment/core app=${REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG} -n ${KUBE_NAMESPACE} --kubeconfig ${KUBECONFIG_FILE}"
          sh "kubectl rollout status deployment/core -n ${KUBE_NAMESPACE} --kubeconfig ${KUBECONFIG_FILE}"
        }
      }
    }
  }
  post {
    always {
      sh "docker logout ${REGISTRY} || true"
    }
  }
}
