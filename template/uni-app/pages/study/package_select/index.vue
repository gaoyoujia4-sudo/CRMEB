<template>
	<view class="package-select">
		<!-- 商品信息 -->
		<view class="product-card acea-row" v-if="productInfo.id">
			<view class="pictrue">
				<image :src="productInfo.image" mode="aspectFill"></image>
			</view>
			<view class="text acea-row row-column-around">
				<view class="name line1">{{ productInfo.store_name }}</view>
				<view class="money">
					{{ $t(`￥`) }}<text class="num">{{ productInfo.price }}</text>
				</view>
			</view>
		</view>

		<!-- 团型 -->
		<view class="tab-bar">
			<view class="tab-item" :class="{ active: groupType === 0 }" @tap="switchGroup(0)">{{ $t(`全部`) }}</view>
			<view class="tab-item" :class="{ active: groupType === 1 }" @tap="switchGroup(1)">{{ $t(`私家团`) }}</view>
			<view class="tab-item" :class="{ active: groupType === 2 }" @tap="switchGroup(2)">{{ $t(`拼团`) }}</view>
		</view>

		<!-- 选择场次 -->
		<view class="section-title">{{ $t(`选择场次`) }}</view>
		<view class="pill-bar acea-row">
			<view class="pill" :class="{ active: sessionType === 0 }" @tap="switchSession(0)">{{ $t(`全部`) }}</view>
			<view class="pill" :class="{ active: sessionType === 1 }" @tap="switchSession(1)">{{ $t(`上午场`) }}</view>
			<view class="pill" :class="{ active: sessionType === 2 }" @tap="switchSession(2)">{{ $t(`下午场`) }}</view>
			<view class="pill" :class="{ active: sessionType === 3 }" @tap="switchSession(3)">{{ $t(`一日游`) }}</view>
		</view>

		<!-- 选择套餐 -->
		<view class="section-title">{{ $t(`选择套餐`) }}（{{ packageList.length }}）</view>
		<view class="package-list">
			<view
				class="package-item"
				:class="{ active: selectedPackage && selectedPackage.id === item.id }"
				v-for="(item, index) in packageList"
				:key="index"
				@tap="selectPackage(item)"
			>
				<view class="title">{{ packageTitle(item) }}</view>
				<view class="price-row acea-row row-middle">
					<view class="money">{{ $t(`￥`) }}<text class="num">{{ item.price }}</text></view>
					<view class="y-money" v-if="Number(item.ot_price) > 0">{{ $t(`￥`) }}{{ item.ot_price }}</view>
				</view>
			</view>
			<view class="empty" v-if="!packageList.length && !loading">{{ $t(`暂无可选套餐`) }}</view>
		</view>

		<!-- 选择日期 -->
		<view class="section-title">{{ $t(`选择日期`) }}</view>
		<scroll-view scroll-x class="date-scroll" :show-scrollbar="false">
			<view class="date-bar acea-row">
				<view
					class="date-item"
					:class="{ active: travelDate === item.value }"
					v-for="(item, index) in dateList"
					:key="index"
					@tap="selectDate(item)"
				>
					<view class="week">{{ item.week }}</view>
					<view class="day">{{ item.label }}</view>
				</view>
			</view>
		</scroll-view>

		<!-- 底部操作栏 -->
		<view class="footer-bar acea-row row-between-wrapper">
			<view class="total">
				<template v-if="selectedPackage">
					{{ $t(`￥`) }}<text class="num">{{ selectedPackage.price }}</text>
				</template>
				<template v-else>{{ $t(`￥`) }}--</template>
			</view>
			<view class="next-btn" :class="{ disabled: !canNext }" @tap="goNext">{{ $t(`下一步`) }}</view>
		</view>
	</view>
</template>

<script>
	import { studyPackageListApi } from "@/api/study";
	import { getProductDetail, postCartAdd } from "@/api/store";
	import { toLogin } from "@/libs/login.js";
	import { mapGetters } from "vuex";

	const WEEK_NAMES = ["周日", "周一", "周二", "周三", "周四", "周五", "周六"];

	export default {
		data() {
			return {
				productId: 0,
				productInfo: {},
				groupType: 0, //团型：0全部 1私家团 2拼团
				sessionType: 0, //场次：0全部 1上午场 2下午场 3一日游
				packageList: [],
				selectedPackage: null,
				travelDate: "",
				dateList: [],
				loading: false,
			};
		},
		computed: {
			...mapGetters(["isLogin"]),
			canNext() {
				return this.selectedPackage && this.travelDate;
			},
		},
		onLoad(option) {
			this.productId = parseInt(option.product_id || 0);
			if (!this.productId) {
				this.$util.Tips({ title: this.$t(`参数错误`) }, () => {
					uni.navigateBack();
				});
				return;
			}
			this.buildDateList();
			this.getProduct();
			this.getPackages();
		},
		methods: {
			// 商品信息
			getProduct() {
				getProductDetail(this.productId).then((res) => {
					this.productInfo = res.data.storeInfo || {};
				});
			},
			// 套餐列表
			getPackages() {
				this.loading = true;
				this.selectedPackage = null;
				studyPackageListApi({
					product_id: this.productId,
					group_type: this.groupType,
					session_type: this.sessionType,
				})
					.then((res) => {
						this.packageList = res.data || [];
						this.loading = false;
					})
					.catch(() => {
						this.loading = false;
					});
			},
			// 套餐展示名：场次+团型+名称
			packageTitle(item) {
				let session = ["", "上午场", "下午场", "一日游"][item.session_type] || "";
				let group = ["", "私家团", "拼团"][item.group_type] || "";
				let prefix = session ? session + "-" : "";
				let groupTag = group ? "【" + group + "】" : "";
				return prefix + groupTag + item.title;
			},
			switchGroup(type) {
				if (this.groupType === type) return;
				this.groupType = type;
				this.getPackages();
			},
			switchSession(type) {
				if (this.sessionType === type) return;
				this.sessionType = type;
				this.getPackages();
			},
			selectPackage(item) {
				this.selectedPackage = item;
			},
			// 生成未来30天日期（含周几）
			buildDateList() {
				let list = [];
				let now = new Date();
				for (let i = 0; i < 30; i++) {
					let d = new Date(now.getFullYear(), now.getMonth(), now.getDate() + i);
					let m = d.getMonth() + 1;
					let day = d.getDate();
					let pad = (n) => (n < 10 ? "0" + n : "" + n);
					list.push({
						value: d.getFullYear() + "-" + pad(m) + "-" + pad(day),
						week: i === 0 ? this.$t(`今天`) : WEEK_NAMES[d.getDay()],
						label: m + "月" + day + "日",
					});
				}
				this.dateList = list;
			},
			selectDate(item) {
				this.travelDate = item.value;
			},
			// 下一步：生成购买记录并进入订单确认页
			goNext() {
				if (!this.selectedPackage) {
					return this.$util.Tips({ title: this.$t(`请选择套餐`) });
				}
				if (!this.travelDate) {
					return this.$util.Tips({ title: this.$t(`请选择出行日期`) });
				}
				if (!this.isLogin) {
					return toLogin();
				}
				postCartAdd({
					productId: this.productId,
					cartNum: 1,
					new: 1,
					uniqueId: "",
					studyPackageId: this.selectedPackage.id,
					studyTravelDate: this.travelDate,
				})
					.then((res) => {
						uni.navigateTo({
							url: "/pages/goods/order_confirm/index?new=1&cartId=" + res.data.cartId,
						});
					})
					.catch((err) => {
						return this.$util.Tips({ title: err });
					});
			},
		},
	};
</script>

<style lang="scss" scoped>
	.package-select {
		padding-bottom: 140rpx;
		background-color: #f5f5f5;
		min-height: 100vh;
	}

	.product-card {
		background-color: #fff;
		padding: 24rpx;
		margin-bottom: 16rpx;

		.pictrue {
			width: 120rpx;
			height: 120rpx;
			border-radius: 12rpx;
			overflow: hidden;

			image {
				width: 100%;
				height: 100%;
			}
		}

		.text {
			flex: 1;
			margin-left: 20rpx;
			height: 120rpx;

			.name {
				font-size: 28rpx;
				font-weight: bold;
				color: #333;
			}

			.money {
				color: var(--view-theme, #e93323);
				font-size: 24rpx;

				.num {
					font-size: 36rpx;
					font-weight: bold;
				}
			}
		}
	}

	.tab-bar {
		display: flex;
		background-color: #fff;
		padding: 20rpx 24rpx;

		.tab-item {
			flex: 1;
			text-align: center;
			font-size: 28rpx;
			color: #666;
			padding: 12rpx 0;
			border-radius: 32rpx;
			margin: 0 12rpx;
			background-color: #f5f5f5;

			&.active {
				color: #fff;
				background-color: var(--view-theme, #e93323);
				font-weight: bold;
			}
		}
	}

	.section-title {
		font-size: 28rpx;
		font-weight: bold;
		color: #333;
		padding: 28rpx 24rpx 16rpx;
	}

	.pill-bar {
		padding: 0 24rpx;
		flex-wrap: wrap;

		.pill {
			font-size: 26rpx;
			color: #666;
			background-color: #fff;
			border: 1rpx solid #e5e5e5;
			border-radius: 32rpx;
			padding: 12rpx 36rpx;
			margin-right: 20rpx;
			margin-bottom: 16rpx;

			&.active {
				color: var(--view-theme, #e93323);
				border-color: var(--view-theme, #e93323);
				font-weight: bold;
			}
		}
	}

	.package-list {
		padding: 0 24rpx;

		.package-item {
			background-color: #fff;
			border: 2rpx solid transparent;
			border-radius: 16rpx;
			padding: 24rpx;
			margin-bottom: 20rpx;

			&.active {
				border-color: var(--view-theme, #e93323);
				background-color: rgba(233, 51, 35, 0.04);
			}

			.title {
				font-size: 28rpx;
				color: #333;
				line-height: 1.5;
			}

			.price-row {
				margin-top: 16rpx;

				.money {
					color: var(--view-theme, #e93323);
					font-size: 24rpx;

					.num {
						font-size: 34rpx;
						font-weight: bold;
					}
				}

				.y-money {
					margin-left: 16rpx;
					font-size: 24rpx;
					color: #999;
					text-decoration: line-through;
				}
			}
		}

		.empty {
			text-align: center;
			color: #999;
			font-size: 26rpx;
			padding: 60rpx 0;
		}
	}

	.date-scroll {
		white-space: nowrap;
		padding: 0 24rpx;
		box-sizing: border-box;
		width: 100%;

		.date-bar {
			display: inline-flex;
		}

		.date-item {
			display: inline-flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			width: 140rpx;
			padding: 20rpx 0;
			margin-right: 16rpx;
			background-color: #fff;
			border: 2rpx solid transparent;
			border-radius: 16rpx;

			&.active {
				border-color: var(--view-theme, #e93323);
				background-color: rgba(233, 51, 35, 0.04);

				.week,
				.day {
					color: var(--view-theme, #e93323);
					font-weight: bold;
				}
			}

			.week {
				font-size: 24rpx;
				color: #999;
			}

			.day {
				font-size: 26rpx;
				color: #333;
				margin-top: 8rpx;
			}
		}
	}

	.footer-bar {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #fff;
		padding: 16rpx 24rpx;
		padding-bottom: calc(16rpx + env(safe-area-inset-bottom));
		box-shadow: 0 -2rpx 12rpx rgba(0, 0, 0, 0.06);
		z-index: 99;

		.total {
			color: var(--view-theme, #e93323);
			font-size: 28rpx;

			.num {
				font-size: 44rpx;
				font-weight: bold;
			}
		}

		.next-btn {
			background-color: var(--view-theme, #e93323);
			color: #fff;
			font-size: 30rpx;
			font-weight: bold;
			border-radius: 44rpx;
			padding: 18rpx 80rpx;

			&.disabled {
				opacity: 0.5;
			}
		}
	}
</style>
