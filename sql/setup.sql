USE sipn_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('aluno', 'funcionario') NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE entidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(20) NOT NULL UNIQUE,
    email_comercial VARCHAR(100) NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entidade_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    carga_horaria VARCHAR(20) NOT NULL,
    imagem_capa VARCHAR(255) NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entidade_id) REFERENCES entidades(id) ON DELETE CASCADE
);

CREATE TABLE aulas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    duracao VARCHAR(10) NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    curso_id INT NOT NULL,
    status ENUM('em_andamento', 'concluido') DEFAULT 'em_andamento',
    data_matricula DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

CREATE TABLE progresso_aulas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula_id INT NOT NULL,
    aula_id INT NOT NULL,
    data_conclusao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matricula_id) REFERENCES matriculas(id) ON DELETE CASCADE,
    FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
);

CREATE TABLE certificados (
    codigo_autenticacao VARCHAR(50) PRIMARY KEY,
    aluno_id INT NOT NULL,
    curso_id INT,
    curso_titulo_snapshot VARCHAR(150) NOT NULL,
    entidade_nome_snapshot VARCHAR(100) NOT NULL,
    carga_horaria_snapshot VARCHAR(20) NOT NULL,
    data_emissao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE SET NULL
);

INSERT INTO usuarios (id, nome, email, senha, tipo) VALUES
(1, 'Admin Principal', 'admin@sipn.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'funcionario'),
(2, 'Roberto Alves', 'roberto@sipn.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'funcionario'),
(3, 'Ana Silva', 'ana@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno'),
(4, 'Carlos Souza', 'carlos@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno'),
(5, 'Beatriz Lima', 'beatriz@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno'),
(6, 'João Pedro', 'joao@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno');

INSERT INTO entidades (id, nome, cnpj, email_comercial) VALUES
(1, 'Fatec', '11.111.111/0001-11', 'contato@fatec.edu.br'),
(2, 'Sebrae', '22.222.222/0001-22', 'parcerias@sebrae.com.br'),
(3, 'Instituto Tecnológico', '33.333.333/0001-33', 'diretoria@itech.org'),
(4, 'Tech Academy', '44.444.444/0001-44', 'hello@techacademy.com'),
(5, 'SIPN Design School', '55.555.555/0001-55', 'cursos@sipndesign.com');

INSERT INTO cursos (id, entidade_id, titulo, descricao, carga_horaria, imagem_capa) VALUES
(1, 1, 'Desenvolvimento Back-End com PHP', 'Aprenda a criar aplicações robustas utilizando PHP e MySQL.', '60h', 'https://images.unsplash.com/photo-1599507593499-a3f7d7d97667?w=800&q=80'),
(2, 2, 'Marketing Digital na Prática', 'Estratégias completas de marketing digital e SEO.', '40h', 'https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=800&q=80'),
(3, 3, 'Gestão Ágil de Projetos', 'Implemente metodologias ágeis como Scrum e Kanban.', '20h', 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=800&q=80'),
(4, 4, 'Introdução à Inteligência Artificial', 'Conceitos fundamentais de IA e Machine Learning.', '50h', 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=800&q=80'),
(5, 5, 'Design de Interfaces (UI/UX)', 'Princípios de usabilidade e experiência do usuário.', '45h', 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&q=80');

INSERT INTO aulas (id, curso_id, titulo, duracao, video_url) VALUES
(1, 1, 'Introdução ao PHP e Servidores', '15:00', 'https://www.youtube.com/watch?v=nUnR8Q_hF4U'),
(2, 1, 'Lógica de Programação e Variáveis', '20:30', 'https://www.youtube.com/watch?v=RLFedoJtd5U'),
(3, 1, 'Estruturas de Repetição e Condicionais', '25:15', 'https://www.youtube.com/watch?v=itqRPjZdbVQ'),
(4, 2, 'O que é Inbound Marketing', '10:00', 'https://www.youtube.com/watch?v=SMKRj4YGfFA'),
(5, 2, 'Criação de Personas', '14:20', 'https://www.youtube.com/watch?v=N9abxb_plRQ'),
(6, 3, 'O Manifesto Ágil', '12:30', 'https://www.youtube.com/watch?v=lvbXAbLFtuo'),
(7, 3, 'Framework Scrum', '28:45', 'https://www.youtube.com/watch?v=tSot0AwL3lM'),
(8, 4, 'História da IA', '18:15', 'https://www.youtube.com/watch?v=HKsLqsCwO10'),
(9, 4, 'Machine Learning', '25:40', 'https://www.youtube.com/watch?v=rhkrBymAP6U'),
(10, 5, 'Diferença entre UI e UX', '15:20', 'https://www.youtube.com/watch?v=lZqSUSewvKU');

INSERT INTO matriculas (id, aluno_id, curso_id, status) VALUES
(1, 3, 1, 'concluido'),
(2, 3, 5, 'em_andamento'),
(3, 4, 1, 'em_andamento');

INSERT INTO progresso_aulas (matricula_id, aula_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 10),
(3, 1);

INSERT INTO certificados (codigo_autenticacao, aluno_id, curso_id, curso_titulo_snapshot, entidade_nome_snapshot, carga_horaria_snapshot) VALUES
('CERT-2026-A1B2C3D4', 3, 1, 'Desenvolvimento Back-End com PHP', 'Fatec', '60h');