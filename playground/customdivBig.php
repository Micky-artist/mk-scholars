<div class="scholarship-card">
								<div class="card-image">
									<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $getScholarships['scholarshipImage'] ?>"
										alt="<?php echo $getScholarships['scholarshipTitle'] ?>">
									<div class="image-overlay"></div>
								</div>

								<div class="card-content">
									<h3 class="card-title">
										<a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>">
											<?php echo $getScholarships['scholarshipTitle'] ?>
										</a>
									</h3>

									<div class="card-description">
										<p><?php echo $getScholarships['scholarshipDetails'] ?></p>
									</div>

									<div class="card-footer">
										<div class="date-info">
											<svg class="calendar-icon" viewBox="0 0 24 24">
												<path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5z" />
											</svg>
											<span><?php echo $getScholarships['scholarshipUpdateDate'] ?></span>
										</div>

										<div class="card-actions">
											<a href="#" class="apply-button">
												<span>Apply Now</span>
												<div class="button-hover-effect"></div>
											</a>

											<a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>"
												class="read-more-button">
												<span>Read More</span>
												<div class="button-arrow">→</div>
											</a>
										</div>
									</div>
								</div>
							</div>
                            <style>
						.scholarship-card {
							position: relative;
							width: 100%;
							max-width: 400px;
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
							height: 240px;
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
							padding: 1.5rem;
							position: relative;
						}

						.card-title {
							font-size: 1.4rem;
							margin: 0 0 1rem 0;
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
						}

						.card-title a:hover {
							background-position: 0% 100%;
						}

						.card-description {
							height: 4.5em;
							/* 3 lines * 1.5em line-height */
							overflow: hidden;
							margin-bottom: 1.5rem;
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
							font-size: 0.9rem;
						}

						.calendar-icon {
							width: 18px;
							height: 18px;
							fill: #636e72;
						}

						.card-actions {
							display: flex;
							gap: 0.75rem;
						}

						.apply-button {
							position: relative;
							display: inline-flex;
							align-items: center;
							padding: 0.6rem 1.2rem;
							background: linear-gradient(135deg, #ff6b6b, #a855f7);
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
						}

						.read-more-button {
							position: relative;
							display: inline-flex;
							align-items: center;
							padding: 0.6rem 1.2rem;
							background: transparent;
							border: 2px solid #e0e0e0;
							border-radius: 8px;
							color: #2d3436;
							text-decoration: none;
							transition: all 0.3s ease;
						}

						.read-more-button:hover {
							border-color: #a855f7;
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