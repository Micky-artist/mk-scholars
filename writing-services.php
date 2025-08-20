<?php
include("./dbconnection/connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<?php include("./partials/head.php") ?>


<body>
	<div class="main-page-wrapper">

		<?php
		include("./partials/navigation.php");
		?>

		<!-- 
			=============================================
				Theme Inner Banner
			============================================== 
			-->

		<div class="theme-inner-banner" style="background: url(images/home/banner-2.jpg) no-repeat center;background-size:cover;">
			<div class="opacity">
				<div class="container">
					<h3>WRITING SERVICES</h3>
					<ul>
						<li><a href="home">Home</a></li>
						<li>/</li>
						<li>Writing Services</li>
					</ul>
				</div> <!-- /.container -->
			</div> <!-- /.opacity -->
		</div> <!-- /.theme-inner-banner -->



		<!-- 
			=============================================
				Featured Course 3 Column
			============================================== 
		-->

		<div class="writing-services-main">
			<div class="container">
				<div class="row">
					<div class="col-lg-8 col-md-12">
						<!-- Hero Section -->
						<div class="writing-hero">
							<div class="hero-content">
								<h1>Professional Writing & Proofreading Services</h1>
								<p class="hero-subtitle">Expert assistance for your academic and professional success</p>
								<div class="hero-stats">
									<div class="stat-item">
										<span class="stat-number">500+</span>
										<span class="stat-label">Documents Written</span>
									</div>
									<div class="stat-item">
										<span class="stat-number">98%</span>
										<span class="stat-label">Success Rate</span>
									</div>
									<div class="stat-item">
										<span class="stat-number">24h</span>
										<span class="stat-label">Fast Turnaround</span>
									</div>
								</div>
							</div>
						</div>

						<!-- Services Overview -->
						<div class="services-overview">
							<h2>Our Services</h2>
							<p class="section-description">We specialize in creating compelling, professional documents that help you stand out in your academic and professional pursuits.</p>
							
							<div class="services-grid">
								<div class="service-card">
									<div class="service-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
											<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
											<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
										</svg>
									</div>
									<h3>Personal Statements & Essays</h3>
									<p>Craft compelling personal statements and essays that showcase your unique story, achievements, and aspirations for university applications, scholarships, and academic programs.</p>
									<ul class="service-features">
										<li>University applications</li>
										<li>Scholarship essays</li>
										<li>Academic writing</li>
										<li>Personal narratives</li>
									</ul>
								</div>

								<div class="service-card">
									<div class="service-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
											<path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
											<path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h1V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
										</svg>
									</div>
									<h3>Resume & CV</h3>
									<p>Professional resume and CV writing services tailored to your industry, experience level, and career goals. Stand out to employers with compelling, ATS-friendly documents.</p>
									<ul class="service-features">
										<li>Professional resumes</li>
										<li>Academic CVs</li>
										<li>ATS optimization</li>
										<li>Industry-specific formats</li>
									</ul>
								</div>

								<div class="service-card">
									<div class="service-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
											<path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
										</svg>
									</div>
									<h3>Recommendation & Reference Letters</h3>
									<p>Professional recommendation and reference letters that highlight your strengths, achievements, and character. Perfect for academic applications, job searches, and professional opportunities.</p>
									<ul class="service-features">
										<li>Academic references</li>
										<li>Professional recommendations</li>
										<li>Character references</li>
										<li>Customized content</li>
									</ul>
								</div>

								<div class="service-card">
									<div class="service-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-chat-quote" viewBox="0 0 16 16">
											<path d="M2.5 3a.5.5 0 0 0 0 1h11a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h8a.5.5 0 0 0 0-1z"/>
											<path d="M5 4.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1z"/>
										</svg>
									</div>
									<h3>Motivation Letters & Statements of Purpose</h3>
									<p>Compelling motivation letters and statements of purpose that clearly articulate your goals, passion, and suitability for academic programs, scholarships, or job opportunities.</p>
									<ul class="service-features">
										<li>Graduate school applications</li>
										<li>Scholarship applications</li>
										<li>Job applications</li>
										<li>Research proposals</li>
									</ul>
								</div>

								<div class="service-card">
									<div class="service-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
											<path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5M1 4.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5z"/>
										</svg>
									</div>
									<h3>Cover Letters & Portfolios</h3>
									<p>Professional cover letters and portfolio development services that showcase your skills, experience, and achievements in the most compelling way possible.</p>
									<ul class="service-features">
										<li>Job cover letters</li>
										<li>Portfolio development</li>
										<li>Project showcases</li>
										<li>Creative presentations</li>
									</ul>
								</div>
							</div>
						</div>

						<!-- Why Choose Us -->
						<div class="why-choose-us">
							<h2>Why Choose MK Scholars Writing Services?</h2>
							<div class="benefits-grid">
								<div class="benefit-item">
									<div class="benefit-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
											<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.97a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									</div>
									<div class="benefit-content">
										<h4>Experienced Writers</h4>
										<p>Our team consists of qualified professionals with extensive experience in academic and professional writing.</p>
									</div>
								</div>

								<div class="benefit-item">
									<div class="benefit-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
											<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.97a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									</div>
									<div class="benefit-content">
										<h4>100% Confidential</h4>
										<p>Your privacy and confidentiality are our top priorities. All information shared with us is kept strictly confidential.</p>
									</div>
								</div>

								<div class="benefit-item">
									<div class="benefit-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
											<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.97a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									</div>
									<div class="benefit-content">
										<h4>Fast Turnaround</h4>
										<p>We understand deadlines are crucial. We offer quick turnaround times without compromising on quality.</p>
									</div>
								</div>

								<div class="benefit-item">
									<div class="benefit-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
											<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.97a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									</div>
									<div class="benefit-content">
										<h4>Affordable Pricing</h4>
										<p>Quality writing services at student-friendly prices. We believe professional help should be accessible to everyone.</p>
									</div>
								</div>

								<div class="benefit-item">
									<div class="benefit-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
											<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.97a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									</div>
									<div class="benefit-content">
										<h4>Unlimited Revisions</h4>
										<p>We're committed to your satisfaction. We offer unlimited revisions until you're completely happy with the result.</p>
									</div>
								</div>

								<div class="benefit-item">
									<div class="benefit-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
											<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.97a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									</div>
									<div class="benefit-content">
										<h4>24/7 Support</h4>
										<p>Our customer support team is available around the clock to assist you with any questions or concerns.</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4 col-md-12">
						<!-- Application Form Sidebar -->
						<div class="writing-services-sidebar">
							<div class="application-form-card">
								<h3>Apply for Writing Service</h3>
								<p class="form-description">Fill out the form below to get started with your professional writing service.</p>
								
								<form class="writing-service-form" method="post" action="./php/process-writing-service.php">
									<div class="form-group">
										<label for="service_type">Service Type *</label>
										<select name="service_type" id="service_type" required>
											<option value="">Select a service</option>
											<option value="personal_statement">Personal Statement & Essay</option>
											<option value="resume_cv">Resume & CV</option>
											<option value="recommendation_letter">Recommendation Letter</option>
											<option value="motivation_letter">Motivation Letter</option>
											<option value="cover_letter">Cover Letter & Portfolio</option>
											<option value="other">Other</option>
										</select>
									</div>

									<div class="form-group">
										<label for="full_name">Full Name *</label>
										<input type="text" name="full_name" id="full_name" required>
									</div>

									<div class="form-group">
										<label for="email">Email Address *</label>
										<input type="email" name="email" id="email" required>
									</div>

									<div class="form-group">
										<label for="phone">Phone Number</label>
										<input type="tel" name="phone" id="phone">
									</div>

									<div class="form-group">
										<label for="deadline">Deadline</label>
										<input type="date" name="deadline" id="deadline">
									</div>

									<div class="form-group">
										<label for="document_length">Document Length</label>
										<select name="document_length" id="document_length">
											<option value="">Select length</option>
											<option value="1-2_pages">1-2 pages</option>
											<option value="3-4_pages">3-4 pages</option>
											<option value="5-6_pages">5-6 pages</option>
											<option value="7+_pages">7+ pages</option>
										</select>
									</div>

									<div class="form-group">
										<label for="requirements">Requirements & Details *</label>
										<textarea name="requirements" id="requirements" rows="4" placeholder="Please describe your requirements, target audience, and any specific instructions..." required></textarea>
									</div>

									<div class="form-group">
										<label for="budget">Budget Range</label>
										<select name="budget" id="budget">
											<option value="">Select budget range</option>
											<option value="under_50000">Under 50,000 RWF</option>
											<option value="50000_100000">50,000 - 100,000 RWF</option>
											<option value="100000_200000">100,000 - 200,000 RWF</option>
											<option value="200000_500000">200,000 - 500,000 RWF</option>
											<option value="500000+">500,000+ RWF</option>
										</select>
									</div>

									<button type="submit" class="submit-btn">
										<span>Submit Application</span>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
											<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
										</svg>
									</button>
								</form>
							</div>

							<!-- Contact Information -->
							<div class="contact-info-card">
								<h4>Get in Touch</h4>
								<div class="contact-item">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
										<path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
									</svg>
									<span>+250 798 611 161</span>
								</div>
								<div class="contact-item">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
										<path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104A2 2 0 0 0 2 14h12a2 2 0 0 0 2-2V4.697L8 9.183z"/>
									</svg>
									<span>mkscholars250@gmail.com</span>
								</div>
								<div class="contact-item">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
										<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .5.5h3.5a.5.5 0 0 0 0-1H8z"/>
									</svg>
									<span>24/7 Support Available</span>
								</div>
							</div>
						</div>
					</div>


						<style>
							/* Writing Services Page Styling */
							.writing-services-main {
								padding: 80px 0;
								background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
							}

							/* Hero Section */
							.writing-hero {
								background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
								color: white;
								padding: 60px 40px;
								border-radius: 20px;
								text-align: center;
								margin-bottom: 60px;
								box-shadow: 0 10px 30px rgba(14, 119, 194, 0.3);
							}

							.hero-content h1 {
								font-size: 2.5rem;
								font-weight: 700;
								margin-bottom: 20px;
								line-height: 1.2;
							}

							.hero-subtitle {
								font-size: 1.2rem;
								margin-bottom: 40px;
								opacity: 0.9;
							}

							.hero-stats {
								display: flex;
								justify-content: center;
								gap: 40px;
								flex-wrap: wrap;
							}

							.stat-item {
								text-align: center;
							}

							.stat-number {
								display: block;
								font-size: 2rem;
								font-weight: 700;
								color: #FDC713;
							}

							.stat-label {
								font-size: 0.9rem;
								opacity: 0.8;
							}

							/* Services Overview */
							.services-overview {
								margin-bottom: 60px;
							}

							.services-overview h2 {
								font-size: 2.2rem;
								font-weight: 600;
								margin-bottom: 20px;
								color: #333;
								text-align: center;
							}

							.section-description {
								text-align: center;
								font-size: 1.1rem;
								color: #666;
								margin-bottom: 50px;
								max-width: 800px;
								margin-left: auto;
								margin-right: auto;
							}

							.services-grid {
								display: grid;
								grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
								gap: 30px;
							}

							.service-card {
								background: white;
								padding: 30px;
								border-radius: 16px;
								box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
								transition: all 0.3s ease;
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.service-card:hover {
								transform: translateY(-5px);
								box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
							}

							.service-icon {
								width: 80px;
								height: 80px;
								background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
								border-radius: 50%;
								display: flex;
								align-items: center;
								justify-content: center;
								margin-bottom: 20px;
								color: white;
							}

							.service-card h3 {
								font-size: 1.4rem;
								font-weight: 600;
								margin-bottom: 15px;
								color: #333;
							}

							.service-card p {
								color: #666;
								line-height: 1.6;
								margin-bottom: 20px;
							}

							.service-features {
								list-style: none;
								padding: 0;
								margin: 0;
							}

							.service-features li {
								padding: 5px 0;
								color: #555;
								position: relative;
								padding-left: 20px;
							}

							.service-features li::before {
								content: "✓";
								position: absolute;
								left: 0;
								color: #0E77C2;
								font-weight: bold;
							}

							/* Why Choose Us */
							.why-choose-us {
								margin-bottom: 60px;
							}

							.why-choose-us h2 {
								font-size: 2.2rem;
								font-weight: 600;
								margin-bottom: 40px;
								color: #333;
								text-align: center;
							}

							.benefits-grid {
								display: grid;
								grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
								gap: 25px;
							}

							.benefit-item {
								display: flex;
								align-items: flex-start;
								gap: 15px;
								background: white;
								padding: 25px;
								border-radius: 12px;
								box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
								transition: all 0.3s ease;
							}

							.benefit-item:hover {
								transform: translateY(-3px);
								box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
							}

							.benefit-icon {
								color: #0E77C2;
								flex-shrink: 0;
							}

							.benefit-content h4 {
								font-size: 1.1rem;
								font-weight: 600;
								margin-bottom: 8px;
								color: #333;
							}

							.benefit-content p {
								color: #666;
								line-height: 1.5;
								margin: 0;
							}

							/* Sidebar */
							.writing-services-sidebar {
								position: sticky;
								top: 100px;
							}

							.application-form-card {
								background: white;
								padding: 30px;
								border-radius: 16px;
								box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
								margin-bottom: 30px;
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.application-form-card h3 {
								font-size: 1.5rem;
								font-weight: 600;
								margin-bottom: 15px;
								color: #333;
								text-align: center;
							}

							.form-description {
								text-align: center;
								color: #666;
								margin-bottom: 25px;
								line-height: 1.5;
							}

							.form-group {
								margin-bottom: 20px;
							}

							.form-group label {
								display: block;
								margin-bottom: 8px;
								font-weight: 500;
								color: #333;
							}

							.form-group input,
							.form-group select,
							.form-group textarea {
								width: 100%;
								padding: 12px 15px;
								border: 2px solid #e9ecef;
								border-radius: 8px;
								font-size: 14px;
								transition: all 0.3s ease;
								background: white;
							}

							.form-group input:focus,
							.form-group select:focus,
							.form-group textarea:focus {
								outline: none;
								border-color: #0E77C2;
								box-shadow: 0 0 0 3px rgba(14, 119, 194, 0.1);
							}

							.submit-btn {
								width: 100%;
								padding: 15px;
								background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
								color: white;
								border: none;
								border-radius: 8px;
								font-size: 16px;
								font-weight: 600;
								cursor: pointer;
								transition: all 0.3s ease;
								display: flex;
								align-items: center;
								justify-content: center;
								gap: 10px;
							}

							.submit-btn:hover {
								transform: translateY(-2px);
								box-shadow: 0 8px 25px rgba(14, 119, 194, 0.4);
							}

							.submit-btn svg {
								transition: transform 0.3s ease;
							}

							.submit-btn:hover svg {
								transform: translateX(4px);
							}

							/* Contact Info Card */
							.contact-info-card {
								background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
								color: white;
								padding: 25px;
								border-radius: 16px;
								text-align: center;
							}

							.contact-info-card h4 {
								font-size: 1.3rem;
								font-weight: 600;
								margin-bottom: 20px;
							}

							.contact-item {
								display: flex;
								align-items: center;
								justify-content: center;
								gap: 10px;
								margin-bottom: 15px;
								font-size: 14px;
							}

							.contact-item:last-child {
								margin-bottom: 0;
							}

							.contact-item svg {
								color: #FDC713;
								flex-shrink: 0;
							}

							/* Responsive Design */
							@media (max-width: 768px) {
								.writing-services-main {
									padding: 40px 0;
								}

								.writing-hero {
									padding: 40px 20px;
									margin-bottom: 40px;
								}

								.hero-content h1 {
									font-size: 2rem;
								}

								.hero-stats {
									gap: 20px;
								}

								.services-grid {
									grid-template-columns: 1fr;
									gap: 20px;
								}

								.benefits-grid {
									grid-template-columns: 1fr;
									gap: 20px;
								}

								.writing-services-sidebar {
									position: static;
									margin-top: 40px;
								}
							}

							@media (max-width: 480px) {
								.hero-content h1 {
									font-size: 1.8rem;
								}

								.hero-subtitle {
									font-size: 1rem;
								}

								.hero-stats {
									flex-direction: column;
									gap: 15px;
								}

								.service-card {
									padding: 20px;
								}

								.benefit-item {
									padding: 20px;
								}

								.application-form-card {
									padding: 20px;
								}
							}

							.scholarshipsContainerDiv {
								display: flex;
								flex-direction: row;
								flex-wrap: wrap;
								justify-content: center;
								width: 100%;
								/* background-color: #2d3436; */
							}

							.scholarship-card {
								position: relative;
								width: 100%;
								max-width: 320px;
								/* Reduced width */
								background: #ffffff;
								border-radius: 16px;
								box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
								overflow: hidden;
								transition: transform 0.3s ease, box-shadow 0.3s ease;
								margin: 1rem;
							}

							.scholarship-card:hover {
								transform: translateY(-5px);
								box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
							}

							.card-image {
								position: relative;
								height: 200px;
								overflow: hidden;
							}

							.card-image img {
								width: 100%;
								height: 100%;
								object-fit: cover;
								transition: transform 0.4s ease;
							}

							.scholarship-card:hover .card-image img {
								transform: scale(1.05);
							}

							.image-overlay {
								position: absolute;
								top: 0;
								left: 0;
								width: 100%;
								height: 100%;
								background: linear-gradient(180deg, rgba(0, 0, 0, 0) 40%, rgba(0, 0, 0, 0.7) 100%);
							}

							.card-content {
								padding: 1.25rem;
								position: relative;
							}

							.card-title {
								font-size: 18px;
								margin: 0 0 0.75rem 0;
								line-height: 1.3;
							}

							.card-title a {
								color: #2d3436;
								text-decoration: none;
								background-image: linear-gradient(to right, #2d3436 50%, transparent 50%);
								background-size: 200% 2px;
								background-position: 100% 100%;
								background-repeat: no-repeat;
								transition: background-position 0.3s ease;
								padding: 5px 5px;
							}

							.card-title a:hover {
								background-position: 0% 100%;
							}

							.card-description {
								height: 4.5em;
								/* 3 lines * 1.5em line-height */
								overflow: hidden;
								margin-bottom: 1.25rem;
								/* Adjusted margin */
							}

							.card-description p {
								margin: 0;
								color: #636e72;
								line-height: 1.5em;
								display: -webkit-box;
								-webkit-line-clamp: 3;
								-webkit-box-orient: vertical;
								overflow: hidden;
							}

							.card-footer {
								display: flex;
								justify-content: space-between;
								align-items: center;

							}

							.date-info {
								display: flex;
								align-items: center;
								gap: 0.5rem;
								color: #636e72;
								font-size: 14px;
								/* Adjusted font size */
							}

							.calendar-icon {
								width: 16px;
								/* Adjusted size */
								height: 16px;
								/* Adjusted size */
								fill: #636e72;
							}

							.card-actions {
								display: flex;
								gap: 0.5rem;
								/* Adjusted gap */
							}

							.apply-button {
								position: relative;
								display: inline-flex;
								align-items: center;
								padding: 0.5rem 1rem;
								color: white !important;
								/* Adjusted padding */
								/* background: linear-gradient(135deg, #ff6b6b, #a855f7); */
								background: linear-gradient(135deg, #0E77C2, #083352);
								color: white;
								border-radius: 8px;
								text-decoration: none;
								overflow: hidden;
								transition: transform 0.3s ease;
							}

							.button-hover-effect {
								position: absolute;
								width: 100%;
								height: 100%;
								background: rgba(255, 255, 255, 0.1);
								left: -100%;
								transition: left 0.3s ease;
							}

							.apply-button:hover .button-hover-effect {
								left: 0;
								color: white;
							}

							.read-more-button {
								position: relative;
								display: inline-flex;
								align-items: center;
								padding: 0.5rem 1rem;
								/* Adjusted padding */
								background: transparent;
								border: 2px solid #0E77C2;
								border-radius: 8px;
								color: #2d3436;
								text-decoration: none;
								transition: all 0.3s ease;
							}

							.read-more-button:hover {
								border-color: #083352;
								background: rgba(168, 85, 247, 0.05);
							}

							.button-arrow {
								margin-left: 0.5rem;
								transform: translateX(0);
								transition: transform 0.3s ease;
							}

							.read-more-button:hover .button-arrow {
								transform: translateX(3px);
							}
						</style>
						<style>
							.allScholarshipContainer {
								height: 450px;
								margin-bottom: 20px;
							}

							.image {
								height: 200px;
								overflow: hidden;
								/* Added to contain images properly */
							}

							.image img {
								object-fit: cover;
								width: 100%;
								height: 100%;
								transition: transform 0.3s ease;
								/* Added for hover effect */
							}

							.image img:hover {
								transform: scale(1.05);
								/* Subtle zoom effect on hover */
							}

							.postLineLimit {
								text-overflow: ellipsis;
								display: -webkit-box;
								-webkit-line-clamp: 4;
								line-clamp: 4;
								/* Standard property alongside webkit version */
								-webkit-box-orient: vertical;
								overflow: hidden;
							}

							.DetailWrapper {
								height: 120px;
								overflow: hidden;
							}

							/* Modern Pagination Styling */
							.modern-pagination-wrapper {
								background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
								border-radius: 16px;
								padding: 30px;
								margin: 40px 0;
								box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.pagination-info {
								display: flex;
								justify-content: space-between;
								align-items: center;
								margin-bottom: 25px;
								padding: 0 10px;
							}

							.pagination-text {
								font-size: 14px;
								color: #6c757d;
								font-weight: 500;
							}

							.pagination-total {
								font-size: 14px;
								color: #495057;
								font-weight: 600;
								background: rgba(8, 51, 82, 0.1);
								padding: 6px 12px;
								border-radius: 20px;
							}

							.modern-pagination {
								display: flex;
								justify-content: center;
								margin-bottom: 25px;
							}

							.pagination-list {
								display: flex;
								list-style: none;
								margin: 0;
								padding: 0;
								gap: 8px;
								align-items: center;
								flex-wrap: wrap;
								justify-content: center;
							}

							.pagination-item {
								margin: 0;
							}

							.pagination-link {
								display: flex;
								align-items: center;
								justify-content: center;
								min-width: 44px;
								height: 44px;
								padding: 0 16px;
								border-radius: 12px;
								background: white;
								color: #495057;
								text-decoration: none;
								font-weight: 600;
								font-size: 15px;
								transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
								border: 2px solid transparent;
								box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
								position: relative;
								overflow: hidden;
							}

							.pagination-link:hover {
								background: #667eea;
								color: white;
								transform: translateY(-2px);
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
								border-color: #667eea;
							}

							.pagination-link.pagination-active {
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								color: white;
								border-color: #667eea;
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
								transform: scale(1.05);
							}

							.pagination-link.pagination-prev,
							.pagination-link.pagination-next {
								gap: 8px;
								font-size: 14px;
								min-width: 120px;
							}

							.pagination-icon {
								width: 18px;
								height: 18px;
								transition: transform 0.3s ease;
							}

							.pagination-link:hover .pagination-icon {
								transform: scale(1.2);
							}

							.pagination-link.pagination-prev:hover .pagination-icon {
								transform: translateX(-2px) scale(1.2);
							}

							.pagination-link.pagination-next:hover .pagination-icon {
								transform: translateX(2px) scale(1.2);
							}

							.pagination-ellipsis {
								display: flex;
								align-items: center;
								justify-content: center;
								min-width: 44px;
								height: 44px;
								padding: 0 16px;
							}

							.pagination-dots {
								color: #6c757d;
								font-size: 18px;
								font-weight: 600;
								letter-spacing: 2px;
							}

							.pagination-quick-jump {
								display: flex;
								align-items: center;
								justify-content: center;
								gap: 15px;
								padding: 20px;
								background: rgba(255, 255, 255, 0.7);
								border-radius: 12px;
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.quick-jump-text {
								font-size: 14px;
								color: #495057;
								font-weight: 500;
							}

							.quick-jump-form {
								display: flex;
								gap: 10px;
								align-items: center;
							}

							.quick-jump-input {
								width: 80px;
								height: 40px;
								padding: 8px 12px;
								border: 2px solid #e9ecef;
								border-radius: 8px;
								font-size: 14px;
								text-align: center;
								transition: all 0.3s ease;
								background: white;
							}

							.quick-jump-input:focus {
								outline: none;
								border-color: #667eea;
								box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
							}

							.quick-jump-button {
								height: 40px;
								padding: 8px 16px;
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								color: white;
								border: none;
								border-radius: 8px;
								font-weight: 600;
								font-size: 14px;
								cursor: pointer;
								transition: all 0.3s ease;
							}

							.quick-jump-button:hover {
								transform: translateY(-2px);
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
							}

							/* Responsive Design */
							@media (max-width: 768px) {
								.modern-pagination-wrapper {
									padding: 20px;
									margin: 30px 0;
								}

								.pagination-info {
									flex-direction: column;
									gap: 15px;
									text-align: center;
								}

								.pagination-list {
									gap: 6px;
								}

								.pagination-link {
									min-width: 40px;
									height: 40px;
									font-size: 14px;
								}

								.pagination-link.pagination-prev,
								.pagination-link.pagination-next {
									min-width: 100px;
									font-size: 13px;
								}

								.pagination-quick-jump {
									flex-direction: column;
									gap: 15px;
									text-align: center;
								}

								.quick-jump-form {
									justify-content: center;
								}
							}

							@media (max-width: 480px) {
								.modern-pagination-wrapper {
									padding: 15px;
									margin: 20px 0;
								}

								.pagination-list {
									gap: 4px;
								}

								.pagination-link {
									min-width: 36px;
									height: 36px;
									font-size: 13px;
									padding: 0 12px;
								}

								.pagination-link.pagination-prev,
								.pagination-link.pagination-next {
									min-width: 80px;
									font-size: 12px;
								}

								.pagination-icon {
									width: 16px;
									height: 16px;
								}
							}

							.searchBtn {
								background-color: #083352 !important;
								display: flex !important;
								justify-content: center !important;
								align-items: center !important;
								color: #fff !important;
								border: none !important;
								/* Added to ensure consistent appearance */
								padding: 10px 20px !important;
								/* Added consistent padding */
								cursor: pointer !important;
								/* Added pointer cursor */
								transition: background-color 0.3s ease !important;
								/* Smooth transition */
							}

							.searchBtn:hover {
								background-color: #0a4066 !important;
								/* Slightly lighter on hover */
							}

							.course-menu {
								background-color: #ebebff;
								display: flex;
								justify-content: space-evenly !important;
								align-items: center !important;
								flex-wrap: wrap;
								padding: 15px 0;
								/* Consistent padding top and bottom */
								margin-bottom: 20px;
								/* Added margin below menu */
								border-radius: 6px;
								/* Rounded corners for modern look */
							}

							.course-menu .active {
								background-color: #083352 !important;
								color: #fff !important;
								/* Ensure text is visible on active background */
							}

							.course-menu .tran3s {
								text-transform: uppercase;
								border: 1px solid #083352;
								padding: 8px 15px;
								/* Added consistent padding */
								border-radius: 4px;
								/* Rounded corners */
								margin: 5px;
								/* Added margin for spacing in wrap situations */
								transition: all 0.3s ease;
								/* Renamed from tran3s to be more specific */
								text-decoration: none;
								/* Remove underline from links */
								color: #083352;
								/* Match border color */
							}

							.course-menu .tran3s:hover {
								background-color: #083352 !important;
								color: #fff !important;
							}

							/* Added responsive adjustments */
							@media (max-width: 768px) {
								.course-pagination li a {
									width: 35px;
									height: 35px;
								}

								.course-menu {
									padding: 10px 0;
								}
							}

							/* Mobile-First Search and Filter Styling */
							.mobile-search-section {
								margin-bottom: 30px;
								order: -1; /* Move to top */
							}

							.mobile-search-wrapper {
								background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
								border-radius: 16px;
								padding: 25px;
								box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.mobile-search-form {
								margin-bottom: 25px;
							}

							.search-input-group {
								display: flex;
								gap: 0;
								border-radius: 12px;
								overflow: hidden;
								box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
								background: white;
							}

							.mobile-search-input {
								flex: 1;
								padding: 15px 20px;
								border: none;
								font-size: 16px;
								outline: none;
								background: white;
							}

							.mobile-search-input::placeholder {
								color: #6c757d;
								font-weight: 500;
							}

							.mobile-search-btn {
								padding: 15px 20px;
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								border: none;
								color: white;
								cursor: pointer;
								transition: all 0.3s ease;
								display: flex;
								align-items: center;
								justify-content: center;
								min-width: 60px;
							}

							.mobile-search-btn:hover {
								transform: translateY(-2px);
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
							}

							.search-icon {
								width: 20px;
								height: 20px;
								stroke: currentColor;
							}

							.mobile-countries-section {
								border-top: 1px solid rgba(0, 0, 0, 0.1);
								padding-top: 20px;
							}

							.countries-title {
								font-size: 16px;
								font-weight: 600;
								color: #495057;
								margin-bottom: 15px;
								text-align: center;
							}

							.countries-grid {
								display: grid;
								grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
								gap: 12px;
								max-height: 300px;
								overflow-y: auto;
								padding: 10px;
								background: rgba(255, 255, 255, 0.7);
								border-radius: 12px;
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.country-item {
								display: flex;
								flex-direction: column;
								align-items: center;
								padding: 12px 8px;
								background: white;
								border-radius: 8px;
								text-decoration: none;
								color: #495057;
								font-weight: 500;
								font-size: 14px;
								transition: all 0.3s ease;
								border: 2px solid transparent;
								box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
								text-align: center;
								min-height: 60px;
								justify-content: center;
							}

							.country-item:hover {
								transform: translateY(-2px);
								box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
								border-color: #667eea;
								color: #667eea;
							}

							.country-item.reset-all {
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								color: white;
								font-weight: 600;
							}

							.country-item.reset-all:hover {
								transform: translateY(-2px);
								box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
							}

							.country-name {
								display: block;
								margin-bottom: 2px;
							}

							.country-reset {
								font-size: 12px;
								opacity: 0.8;
							}

							/* Hide desktop sidebar on mobile */
							.desktop-sidebar {
								display: none;
							}

							/* Show mobile search section on mobile */
							.mobile-search-section {
								display: block;
							}

							/* Desktop Styles */
							@media (min-width: 769px) {
								.mobile-search-section {
									display: none;
								}

								.desktop-sidebar {
									display: block;
								}
							}

							/* Mobile Responsive Adjustments */
							@media (max-width: 480px) {
								.mobile-search-wrapper {
									padding: 20px;
								}

								.mobile-search-input {
									padding: 12px 16px;
									font-size: 15px;
								}

								.mobile-search-btn {
									padding: 12px 16px;
									min-width: 50px;
								}

								.countries-grid {
									grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
									gap: 10px;
									max-height: 250px;
								}

								.country-item {
									padding: 10px 6px;
									font-size: 13px;
									min-height: 50px;
								}

								.countries-title {
									font-size: 15px;
									margin-bottom: 12px;
								}
							}

							/* Ensure proper spacing and layout */
							.featured-course {
								margin-top: 0;
							}

							/* Improve mobile menu spacing */
							@media (max-width: 768px) {
								.course-menu {
									margin-top: 20px;
								}
								
								.scholarshipsContainerDiv {
									margin-top: 20px;
								}
							}
						</style>

						<!-- End of row div -->


						<!-- Modern Pagination -->
						<?php if ($total_pages > 1): ?>
							<div class="modern-pagination-wrapper">
								<div class="pagination-info">
									<span class="pagination-text">Showing page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
									<span class="pagination-total">Total: <?php echo $total_records; ?> applications</span>
								</div>
								
								<nav class="modern-pagination" aria-label="Applications pagination">
									<ul class="pagination-list">
										<!-- Previous Button -->
										<?php if ($page > 1): ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($page - 1); ?>" class="pagination-link pagination-prev" aria-label="Previous page">
													<svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
														<path d="M15 18l-6-6 6-6"/>
													</svg>
													<span class="pagination-text">Previous</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- First Page -->
										<?php if ($page > 3): ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink(1); ?>" class="pagination-link">1</a>
											</li>
											<?php if ($page > 4): ?>
												<li class="pagination-item pagination-ellipsis">
													<span class="pagination-dots">•••</span>
												</li>
											<?php endif; ?>
										<?php endif; ?>

										<!-- Page Numbers -->
										<?php
										$start_page = max(1, $page - 1);
										$end_page = min($total_pages, $page + 1);

										for ($i = $start_page; $i <= $end_page; $i++):
										?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($i); ?>" 
												   class="pagination-link <?php echo ($i == $page) ? 'pagination-active' : ''; ?>" 
												   aria-current="<?php echo ($i == $page) ? 'page' : 'false'; ?>">
													<?php echo $i; ?>
												</a>
											</li>
										<?php endfor; ?>

										<!-- Last Page -->
										<?php if ($page < $total_pages - 2): ?>
											<?php if ($page < $total_pages - 3): ?>
												<li class="pagination-item pagination-ellipsis">
													<span class="pagination-dots">•••</span>
												</li>
											<?php endif; ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($total_pages); ?>" class="pagination-link"><?php echo $total_pages; ?></a>
											</li>
										<?php endif; ?>

										<!-- Next Button -->
										<?php if ($page < $total_pages): ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($page + 1); ?>" class="pagination-link pagination-next" aria-label="Next page">
													<span class="pagination-text">Next</span>
													<svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
														<path d="M9 18l6-6-6-6"/>
													</svg>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</nav>

								<!-- Quick Jump -->
								<div class="pagination-quick-jump">
									<span class="quick-jump-text">Go to page:</span>
									<form class="quick-jump-form" method="get" onsubmit="return validatePageInput(this);">
										<?php
										// Preserve existing GET parameters
										foreach ($_GET as $key => $value) {
											if ($key !== 'page') {
												echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
											}
										}
										?>
										<input type="number" name="page" min="1" max="<?php echo $total_pages; ?>" 
											   class="quick-jump-input" placeholder="<?php echo $page; ?>" 
											   aria-label="Page number">
										<button type="submit" class="quick-jump-button">Go</button>
									</form>
								</div>
							</div>
						<?php endif; ?>

						<?php
						// Helper function to generate pagination links preserving existing GET parameters
						function generatePaginationLink($page_num)
						{
							$params = $_GET;
							$params['page'] = $page_num;
							return '?' . http_build_query($params);
						}
						?>
					</div> <!-- /.featured-course -->
				</div> <!-- /.row -->
			</div>
		</div> <!-- /.feature-course-3-column -->




		<!-- 
			=============================================
				Footer
			============================================== 
			-->
		<?php
		include("./partials/footer.php");
		?>



		<!-- Scroll Top Button -->
		<button class="scroll-top tran3s">
			<i class="fa fa-angle-up" aria-hidden="true"></i>
		</button>


		<!-- Js File_________________________________ -->

		<!-- j Query -->
		<script type="text/javascript" src="vendor/jquery.2.2.3.min.js"></script>
		<!-- Bootstrap Select JS -->
		<script type="text/javascript" src="vendor/bootstrap-select/dist/js/bootstrap-select.js"></script>
		<!-- Bootstrap JS -->
		<script type="text/javascript" src="vendor/bootstrap/bootstrap.min.js"></script>

		<!-- Vendor js _________ -->
		<!-- Mega menu  -->
		<script type="text/javascript" src="vendor/bootstrap-mega-menu/js/menu.js"></script>

		<!-- WOW js -->
		<script type="text/javascript" src="vendor/WOW-master/dist/wow.min.js"></script>
		<!-- owl.carousel -->
		<script type="text/javascript" src="vendor/owl-carousel/owl.carousel.min.js"></script>
		<!-- Feedback Star -->
		<script type="text/javascript" src="vendor/rateYo-master/src/jquery.rateyo.js"></script>

		<!-- Theme js -->
		<script type="text/javascript" src="js/theme.js"></script>

		<!-- Pagination JavaScript -->
		<script>
		// Validate page input for quick jump
		function validatePageInput(form) {
			const input = form.querySelector('input[name="page"]');
			const pageNum = parseInt(input.value);
			const maxPage = parseInt(input.getAttribute('max'));
			const minPage = parseInt(input.getAttribute('min'));
			
			if (isNaN(pageNum) || pageNum < minPage || pageNum > maxPage) {
				alert('Please enter a valid page number between ' + minPage + ' and ' + maxPage);
				input.focus();
				return false;
			}
			
			return true;
		}

		// Add smooth scrolling to pagination links
		document.addEventListener('DOMContentLoaded', function() {
			const paginationLinks = document.querySelectorAll('.pagination-link');
			
			paginationLinks.forEach(link => {
				link.addEventListener('click', function(e) {
					// Smooth scroll to top of page when navigating
					if (this.href && this.href.includes('page=')) {
						e.preventDefault();
						const href = this.href;
						
						// Smooth scroll to top
						window.scrollTo({
							top: 0,
							behavior: 'smooth'
						});
						
						// Navigate after scroll animation
						setTimeout(() => {
							window.location.href = href;
						}, 500);
					}
				});
			});

			// Add loading state to pagination buttons
			paginationLinks.forEach(link => {
				link.addEventListener('click', function() {
					if (this.href && this.href.includes('page=')) {
						this.style.pointerEvents = 'none';
						this.style.opacity = '0.7';
						
						// Reset after navigation
						setTimeout(() => {
							this.style.pointerEvents = '';
							this.style.opacity = '';
						}, 1000);
					}
				});
			});
		});
		</script>

		<!-- Writing Services JavaScript -->
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Form submission handling
			const writingForm = document.getElementById('writing-service-form');
			if (writingForm) {
				writingForm.addEventListener('submit', function(e) {
					e.preventDefault();
					
					// Get form data
					const formData = new FormData(this);
					const submitBtn = this.querySelector('.submit-btn');
					const originalText = submitBtn.innerHTML;
					
					// Show loading state
					submitBtn.innerHTML = '<svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-dasharray="31.416" stroke-dashoffset="31.416"><animate attributeName="stroke-dashoffset" dur="2s" values="0;31.416;0" repeatCount="indefinite"/></circle></svg> Submitting...';
					submitBtn.disabled = true;
					
					// Submit form data
					fetch('process-writing-service.php', {
						method: 'POST',
						body: formData
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// Show success message
							showNotification('Thank you! Your writing service request has been submitted successfully. We\'ll contact you soon.', 'success');
							this.reset();
						} else {
							// Show error message
							showNotification(data.message || 'An error occurred. Please try again.', 'error');
						}
					})
					.catch(error => {
						console.error('Error:', error);
						showNotification('An error occurred. Please try again.', 'error');
					})
					.finally(() => {
						// Reset button state
						submitBtn.innerHTML = originalText;
						submitBtn.disabled = false;
					});
				});
			}
			
			// Smooth scrolling for anchor links
			const anchorLinks = document.querySelectorAll('a[href^="#"]');
			anchorLinks.forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const targetId = this.getAttribute('href').substring(1);
					const targetElement = document.getElementById(targetId);
					
					if (targetElement) {
						targetElement.scrollIntoView({
							behavior: 'smooth',
							block: 'start'
						});
					}
				});
			});
			
			// Animate elements on scroll
			const observerOptions = {
				threshold: 0.1,
				rootMargin: '0px 0px -50px 0px'
			};
			
			const observer = new IntersectionObserver(function(entries) {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.style.opacity = '1';
						entry.target.style.transform = 'translateY(0)';
					}
				});
			}, observerOptions);
			
			// Observe elements for animation
			const animateElements = document.querySelectorAll('.service-card, .benefit-item, .application-form-card');
			animateElements.forEach(el => {
				el.style.opacity = '0';
				el.style.transform = 'translateY(30px)';
				el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
				observer.observe(el);
			});
		});
		
		// Notification system
		function showNotification(message, type = 'info') {
			// Remove existing notifications
			const existingNotifications = document.querySelectorAll('.notification');
			existingNotifications.forEach(notification => notification.remove());
			
			// Create notification element
			const notification = document.createElement('div');
			notification.className = `notification notification-${type}`;
			notification.innerHTML = `
				<div class="notification-content">
					<span class="notification-message">${message}</span>
					<button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
				</div>
			`;
			
			// Add styles
			notification.style.cssText = `
				position: fixed;
				top: 20px;
				right: 20px;
				z-index: 10000;
				max-width: 400px;
				border-radius: 8px;
				box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
				animation: slideInRight 0.3s ease;
			`;
			
			// Add to page
			document.body.appendChild(notification);
			
			// Auto remove after 5 seconds
			setTimeout(() => {
				if (notification.parentElement) {
					notification.remove();
				}
			}, 5000);
		}
		
		// Add notification styles
		const notificationStyles = document.createElement('style');
		notificationStyles.textContent = `
			.notification {
				background: white;
				border-left: 4px solid #667eea;
				padding: 15px 20px;
			}
			
			.notification-success {
				border-left-color: #28a745;
			}
			
			.notification-error {
				border-left-color: #dc3545;
			}
			
			.notification-content {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 15px;
			}
			
			.notification-message {
				color: #333;
				font-size: 14px;
				line-height: 1.4;
			}
			
			.notification-close {
				background: none;
				border: none;
				font-size: 20px;
				color: #999;
				cursor: pointer;
				padding: 0;
				width: 24px;
				height: 24px;
				display: flex;
				align-items: center;
				justify-content: center;
				border-radius: 50%;
				transition: all 0.2s ease;
			}
			
			.notification-close:hover {
				background: #f0f0f0;
				color: #666;
			}
			
			@keyframes slideInRight {
				from {
					transform: translateX(100%);
					opacity: 0;
				}
				to {
					transform: translateX(0);
					opacity: 1;
				}
			}
			
			.spinner {
				width: 20px;
				height: 20px;
				animation: spin 1s linear infinite;
			}
			
			@keyframes spin {
				from { transform: rotate(0deg); }
				to { transform: rotate(360deg); }
			}
		`;
		document.head.appendChild(notificationStyles);
		</script>

	</div> <!-- /.main-page-wrapper -->
</body>

</html>