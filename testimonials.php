<?php
// Include navigation
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<?php include("./partials/head.php") ?>

<body>
	<div class="main-page-wrapper">
		<?php include("./partials/navigation.php"); ?>

		<!-- Page Header -->
		<div class="testimonials-page-header">
			<div class="container">
				<div class="header-content">
					<div class="header-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
							<path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828zM3.794.641c.058-.17.301-.17.359 0l.406 1.162a1.89 1.89 0 0 0 1.199 1.199L7.02 3.41c.17.058.17.301 0 .359l-1.262.407a1.89 1.89 0 0 0-1.199 1.199L3.953 6.537c-.058.17-.301.17-.359 0L3.187 5.375a1.89 1.89 0 0 0-1.199-1.199L.726 3.769a.18.18 0 0 1 0-.359l1.262-.406a1.89 1.89 0 0 0 1.199-1.199z"/>
						</svg>
						<span>Success Stories</span>
					</div>
					<h1 class="page-title">Student Success Stories</h1>
					<p class="page-subtitle">Read inspiring testimonials from students who achieved their dreams with MK Scholars. Their journeys showcase the transformative power of education and our commitment to student success.</p>
				</div>
			</div>
		</div>

		<!-- Testimonials Grid -->
		<div class="testimonials-page-content">
			<div class="container">
				<div class="testimonials-grid">
					<!-- Featured Testimonial -->
					<div class="testimonial-card featured-testimonial">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">
								<span>★★★★★</span>
							</div>
						</div>
						<p class="testimonial-text">I am incredibly grateful to MK Scholars for helping me secure a full scholarship to study Pharmacy in Turkey. Their support made my dreams come true. I highly recommend MK Scholars to other students looking for opportunities. They truly care and can help you achieve your goals, just like they did for me!</p>
						<div class="student-profile">
							<div class="avatar">
								<span>J</span>
							</div>
							<div class="student-info">
								<h4>Josue NSHUTI</h4>
								<p>Pharmacy Student</p>
								<div class="university">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
										<path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/>
										<path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>
									</svg>
									<span>Ege University, Turkey</span>
								</div>
							</div>
						</div>
					</div>

					<!-- Regular Testimonials -->
					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">I'm ABIKUNDA NDEKWE ARISTIDE from Rwanda. MK SCHOLARS helped me prepare for the UCAT entrance exam at the University of Rwanda. With their guidance, I passed the exam and secured admission to study Bachelor of Medicine and Surgery. I'm very grateful for their support!</p>
						<div class="student-profile">
							<div class="avatar">A</div>
							<div class="student-info">
								<h4>Abikunda Ndekwe Aristide</h4>
								<p>Medicine Student, University of Rwanda</p>
							</div>
						</div>
					</div>

					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">I am so grateful to MK Scholars for helping me get a Mastercard full scholarship at the University of Rwanda. Their support and guidance made my dream come true. Thank you, MK Scholars, for believing in me and giving me the chance to achieve a brighter future.</p>
						<div class="student-profile">
							<div class="avatar">U</div>
							<div class="student-info">
								<h4>Uwera Peace</h4>
								<p>Scholarship Recipient, University of Rwanda</p>
							</div>
						</div>
					</div>

					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">I am IFEOLUWA OYEBADEJO DESMOND from Nigeria. MK SCHOLARS helped me throughout the entire application process for the Bachelor of Medicine and Surgery at the University of Rwanda and helped me prepare for the UCAT entrance exam, which I successfully passed. I'm very grateful and happy our paths crossed. Contact them if you need help, too!</p>
						<div class="student-profile">
							<div class="avatar">I</div>
							<div class="student-info">
								<h4>Ifeoluwa Oyebadejo Desmond</h4>
								<p>Medicine Student, University of Rwanda</p>
							</div>
						</div>
					</div>

					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">I am very grateful to MK Scholars for helping me get admission and a visa to study in Italy. Their support made the whole process easy and stress-free. Without their help, I wouldn't have achieved this dream. Thank you, MK Scholars, for making it possible!</p>
						<div class="student-profile">
							<div class="avatar">R</div>
							<div class="student-info">
								<h4>Rukundo Desire</h4>
								<p>Student, University of Cassino, Italy</p>
							</div>
						</div>
					</div>

					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">I am very grateful to MK Scholars for helping me secure a full scholarship to study Architecture in Morocco. Their support made my dream possible, and I feel encouraged to pursue my passion. I highly recommend MK Scholars to other students seeking opportunities for their education. Thank you, MK Scholars!</p>
						<div class="student-profile">
							<div class="avatar">J</div>
							<div class="student-info">
								<h4>Rutikanga Jean Damour</h4>
								<p>Architecture Student, Morocco</p>
							</div>
						</div>
					</div>

					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">MK Scholars have been tremendously helpful in obtaining a full scholarship at UGHE, and their help was not in vain. I am filled with gratitude for their assistance!</p>
						<div class="student-profile">
							<div class="avatar">C</div>
							<div class="student-info">
								<h4>Christella Ineza</h4>
								<p>Student, University of Global Health Equity (UGHE)</p>
							</div>
						</div>
					</div>

					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">I'm thankful for MK Scholars because it helped me secure a full paid Scholarship at African Leadership University.</p>
						<div class="student-profile">
							<div class="avatar">S</div>
							<div class="student-info">
								<h4>Salomon Uwimana</h4>
								<p>CTO, M&S Innovation Lab Ltd</p>
							</div>
						</div>
					</div>

					<div class="testimonial-card">
						<div class="card-header">
							<div class="quote-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
									<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
								</svg>
							</div>
							<div class="stars">★★★★★</div>
						</div>
						<p class="testimonial-text">MK Scholars Helped me secure a grant at African Leadership University and I'm Thankful for it.</p>
						<div class="student-profile">
							<div class="avatar">U</div>
							<div class="student-info">
								<h4>Umwari Grace</h4>
								<p>Student At ALU</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Success Stats -->
				<div class="success-stats">
					<div class="stat-item">
						<div class="stat-number">500+</div>
						<div class="stat-label">Students Helped</div>
					</div>
					<div class="stat-item">
						<div class="stat-number">95%</div>
						<div class="stat-label">Success Rate</div>
					</div>
					<div class="stat-item">
						<div class="stat-number">50+</div>
						<div class="stat-label">Countries</div>
					</div>
					<div class="stat-item">
						<div class="stat-number">100+</div>
						<div class="stat-label">Universities</div>
					</div>
				</div>

				<!-- CTA Section -->
				<div class="testimonials-cta">
					<h3>Ready to Write Your Success Story?</h3>
					<p>Join our community of successful scholars and start your journey today</p>
					<a href="./applications" class="cta-button">
						<span>Apply for Scholarships</span>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
							<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
						</svg>
					</a>
				</div>
			</div>
		</div>

		<?php include("./partials/footer.php"); ?>
	</div>

	<style>
		/* Testimonials Page Styles */
		body {
			margin-top: 130px; /* Account for fixed navigation */
		}

		.testimonials-page-header {
			background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
			color: white;
			padding: 100px 0 80px 0;
			text-align: center;
			position: relative;
			overflow: hidden;
		}

		.testimonials-page-header::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="30" cy="30" r="3" fill="rgba(255,255,255,0.05)"/><circle cx="60" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="3" fill="rgba(255,255,255,0.05)"/><circle cx="20" cy="60" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="70" cy="70" r="3" fill="rgba(255,255,255,0.05)"/></svg>');
			animation: float 20s ease-in-out infinite;
		}

		.header-badge {
			display: inline-flex;
			align-items: center;
			gap: 10px;
			background: rgba(255, 255, 255, 0.2);
			color: white;
			padding: 12px 24px;
			border-radius: 50px;
			font-size: 14px;
			font-weight: 600;
			margin-bottom: 30px;
			backdrop-filter: blur(10px);
		}

		.page-title {
			font-size: 4rem;
			font-weight: 800;
			margin: 0 0 20px 0;
			line-height: 1.2;
		}

		.page-subtitle {
			font-size: 1.3rem;
			color: rgba(255, 255, 255, 0.9);
			max-width: 800px;
			margin: 0 auto;
			line-height: 1.7;
		}

		.testimonials-page-content {
			padding: 100px 0;
			background: linear-gradient(135deg, #f8f9fc 0%, #e9ecf4 100%);
		}

		.testimonials-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
			gap: 30px;
			margin-bottom: 80px;
		}

		.featured-testimonial {
			grid-column: 1 / -1;
			max-width: 900px;
			margin: 0 auto;
			background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
			color: white;
		}

		.testimonial-card {
			background: white;
			border-radius: 20px;
			padding: 30px;
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
			transition: all 0.3s ease;
			position: relative;
			overflow: hidden;
		}

		.testimonial-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(135deg, #0E77C2, #FDC713);
			opacity: 0;
			transition: opacity 0.3s ease;
		}

		.testimonial-card:hover::before {
			opacity: 1;
		}

		.testimonial-card:hover {
			transform: translateY(-10px);
			box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
		}

		.card-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 20px;
		}

		.quote-icon {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 60px;
			height: 60px;
			background: linear-gradient(135deg, #FDC713, #F4B942);
			border-radius: 15px;
			color: #083352;
			box-shadow: 0 8px 25px rgba(253, 199, 19, 0.3);
		}

		.featured-testimonial .quote-icon {
			width: 80px;
			height: 80px;
			border-radius: 20px;
		}

		.stars {
			color: #FDC713;
			font-size: 1.2rem;
			text-shadow: 0 2px 8px rgba(253, 199, 19, 0.3);
		}

		.testimonial-text {
			font-size: 1.2rem;
			line-height: 1.8;
			margin-bottom: 25px;
			color: #555;
		}

		.featured-testimonial .testimonial-text {
			color: rgba(255, 255, 255, 0.95);
			font-size: 1.4rem;
		}

		.student-profile {
			display: flex;
			align-items: center;
			gap: 15px;
		}

		.avatar {
			width: 50px;
			height: 50px;
			background: linear-gradient(135deg, #0E77C2, #083352);
			border-radius: 15px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			font-weight: 700;
			font-size: 1.2rem;
			box-shadow: 0 8px 20px rgba(14, 119, 194, 0.3);
		}

		.featured-testimonial .avatar {
			background: linear-gradient(135deg, #FDC713, #F4B942);
			color: #083352;
			width: 60px;
			height: 60px;
			font-size: 1.4rem;
		}

		.student-info h4 {
			margin: 0 0 5px 0;
			font-size: 1.1rem;
			font-weight: 700;
			color: #333;
		}

		.featured-testimonial .student-info h4 {
			color: white;
			font-size: 1.3rem;
		}

		.student-info p {
			margin: 0;
			color: #666;
			font-size: 0.9rem;
		}

		.featured-testimonial .student-info p {
			color: rgba(255, 255, 255, 0.8);
			font-size: 1rem;
		}

		.university {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-top: 5px;
			color: #888;
			font-size: 0.85rem;
		}

		.featured-testimonial .university {
			color: rgba(255, 255, 255, 0.7);
		}

		.success-stats {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 30px;
			margin-bottom: 80px;
			text-align: center;
		}

		.stat-item {
			background: white;
			padding: 40px 20px;
			border-radius: 20px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
			transition: all 0.3s ease;
		}

		.stat-item:hover {
			transform: translateY(-5px);
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
		}

		.stat-number {
			font-size: 3rem;
			font-weight: 800;
			background: linear-gradient(135deg, #0E77C2, #FDC713);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
			margin-bottom: 10px;
		}

		.stat-label {
			color: #666;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 1px;
			font-size: 0.9rem;
		}

		.testimonials-cta {
			text-align: center;
			background: white;
			padding: 60px 40px;
			border-radius: 25px;
			box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
		}

		.testimonials-cta h3 {
			font-size: 2.5rem;
			font-weight: 700;
			margin: 0 0 15px 0;
			background: linear-gradient(135deg, #0E77C2, #083352);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
		}

		.testimonials-cta p {
			font-size: 1.2rem;
			color: #666;
			margin-bottom: 40px;
		}

		.cta-button {
			display: inline-flex;
			align-items: center;
			gap: 12px;
			background: linear-gradient(135deg, #0E77C2, #083352);
			color: white;
			padding: 20px 40px;
			border-radius: 50px;
			text-decoration: none;
			font-weight: 600;
			font-size: 1.2rem;
			box-shadow: 0 15px 40px rgba(14, 119, 194, 0.3);
			transition: all 0.3s ease;
		}

		.cta-button:hover {
			transform: translateY(-3px);
			box-shadow: 0 20px 50px rgba(14, 119, 194, 0.4);
			color: white;
			text-decoration: none;
		}

		.cta-button svg {
			transition: transform 0.3s ease;
		}

		.cta-button:hover svg {
			transform: translateX(5px);
		}

		/* Responsive Design */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2.5rem;
			}

			.page-subtitle {
				font-size: 1.1rem;
			}

			.testimonials-grid {
				grid-template-columns: 1fr;
				gap: 20px;
			}

			.testimonial-card {
				padding: 25px;
			}

			.testimonials-cta {
				padding: 40px 25px;
			}

			.testimonials-cta h3 {
				font-size: 2rem;
			}
		}

		@media (max-width: 480px) {
			.page-title {
				font-size: 2rem;
			}

			.header-badge {
				padding: 10px 20px;
				font-size: 12px;
			}

			.testimonial-card {
				padding: 20px;
			}

			.testimonial-text {
				font-size: 1.1rem;
			}
		}
	</style>
</body>
</html>
