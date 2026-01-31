CREATE TABLE shu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anggota_id INT,
  tahun INT,
  jumlah DECIMAL(15,2),
  FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);

CREATE TABLE rat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tahun INT,
  tanggal DATE,
  status ENUM('DRAFT','DISAHKAN')
);
