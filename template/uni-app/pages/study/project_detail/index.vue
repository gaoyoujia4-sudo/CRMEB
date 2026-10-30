<template>
	<view class="project-detail" v-if="info.id">
		<view class="cover">
			<image :src="info.image" mode="aspectFill" v-if="info.image"></image>
			<view class="cover-empty" v-else>{{ $t(`暂无封面图`) }}</view>
		</view>
		<view class="card">
			<view class="title">{{ info.title }}</view>
			<view class="money">
				<text class="num">{{ $t(`￥`) }}{{ info.price }}</text>
				<text class="unit">{{ info.price_type == 1 ? $t(`/人`) : $t(`/团`) }}</text>
				<text class="y-money" v-if="info.ot_price">{{ $t(`￥`) }}{{ info.ot_price }}</text>
				<view class="tag">{{ info.price_type == 1 ? $t(`按人计价`) : $t(`按团计价`) }}</view>
			</view>
		</view>
		<view class="card" v-if="info.lecturers && info.lecturers.length">
			<view class="block-title">{{ $t(`讲师`) }}</view>
			<view class="lecturer-list">
				<view class="lecturer-item" v-for="(item, index) in info.lecturers" :key="index">
					<text class="dot"></text>{{ item.name }}
				</view>
			</view>
		</view>
		<view class="card" v-if="info.intro">
			<view class="block-title">{{ $t(`项目介绍`) }}</view>
			<view class="intro">{{ info.intro }}</view>
		</view>
		<view class="buy-bar">
			<view class="buy-btn" :class="{ disabled: !info.product_id }" @tap="goBuy">
				{{ info.product_id ? $t(`立即购买`) : $t(`暂未开放购买`) }}
			</view>
		</view>
	</view>
</template>

<script>
import { studyProjectDetailApi } from "@/api/study.js";
import { toLogin } from "@/libs/login.js";
import { mapGetters } from "vuex";

export default {
	computed: mapGetters(["isLogin"]),
	data() {
		return {
			id: 0,
			info: {},
		};
	},
	onLoad(options) {
		this.id = options.id || 0;
		this.getDetail();
	},
	methods: {
		getDetail() {
			let that = this;
			studyProjectDetailApi(that.id)
				.then((res) => {
					that.info = (res.data && res.data.info) || {};
				})
				.catch((err) => {
					that.$util.Tips({ title: err });
				});
		},
		goBuy() {
			if (this.isLogin === false) return toLogin();
			let productId = Number(this.info.product_id || 0);
			if (!productId) {
				this.$util.Tips({ title: "该研学项目暂未开放购买" });
				return;
			}
			uni.navigateTo({
				url: "/pages/goods_details/index?id=" + productId,
			});
		},
	},
};
</script>

<style scoped lang="scss">
.project-detail {
	min-height: 100vh;
	background-color: #f5f5f5;
	padding-bottom: 140rpx;
}

.cover {
	width: 100%;
	height: 500rpx;
	background-color: #eee;

	image {
		width: 100%;
		height: 100%;
	}

	.cover-empty {
		width: 100%;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #999;
		font-size: 28rpx;
	}
}

.card {
	background-color: #fff;
	border-radius: 16rpx;
	margin: 20rpx 24rpx 0;
	padding: 28rpx;

	.title {
		font-size: 34rpx;
		font-weight: bold;
		color: #282828;
	}

	.money {
		display: flex;
		align-items: baseline;
		margin-top: 20rpx;
		font-size: 24rpx;
		color: #e93323;

		.num {
			font-size: 44rpx;
			font-weight: bold;
		}

		.unit {
			font-size: 24rpx;
			color: #999;
		}

		.y-money {
			font-size: 26rpx;
			color: #999;
			text-decoration: line-through;
			margin-left: 16rpx;
		}

		.tag {
			margin-left: auto;
			padding: 6rpx 20rpx;
			border-radius: 24rpx;
			font-size: 24rpx;
			color: #e93323;
			background-color: rgba(233, 51, 35, 0.08);
		}
	}

	.block-title {
		font-size: 30rpx;
		font-weight: bold;
		color: #282828;
		margin-bottom: 20rpx;
	}

	.lecturer-list {
		.lecturer-item {
			font-size: 28rpx;
			color: #555;
			line-height: 56rpx;

			.dot {
				display: inline-block;
				width: 12rpx;
				height: 12rpx;
				border-radius: 50%;
				background-color: #e93323;
				margin-right: 16rpx;
				vertical-align: middle;
			}
		}
	}

	.intro {
		font-size: 28rpx;
		color: #555;
		line-height: 46rpx;
		white-space: pre-wrap;
	}
}

.buy-bar {
	position: fixed;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #fff;
	padding: 16rpx 24rpx;
	padding-bottom: calc(16rpx + env(safe-area-inset-bottom));
	box-shadow: 0 -2rpx 12rpx rgba(0, 0, 0, 0.05);

	.buy-btn {
		height: 88rpx;
		line-height: 88rpx;
		text-align: center;
		border-radius: 44rpx;
		background-color: #e93323;
		color: #fff;
		font-size: 30rpx;
		font-weight: bold;

		&.disabled {
			background-color: #ccc;
		}
	}
}
</style>
