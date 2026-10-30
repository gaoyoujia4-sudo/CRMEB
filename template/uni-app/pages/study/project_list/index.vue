<template>
	<view class="project-list">
		<view class="list">
			<view
				class="item acea-row"
				v-for="(item, index) in projectList"
				:key="index"
				@tap="goDetail(item)"
			>
				<view class="pictrue">
					<image :src="item.image" mode="aspectFill"></image>
				</view>
				<view class="text acea-row row-column-between">
					<view class="name line2">{{ item.title }}</view>
					<view class="lecturer line1" v-if="item.lecturer_name">{{ $t(`讲师`) }}：{{ item.lecturer_name }}</view>
					<view class="bottom acea-row row-between-wrapper">
						<view class="money">
							<text class="num">{{ $t(`￥`) }}{{ item.price }}</text>
							<text class="unit">{{ item.price_type == 1 ? $t(`/人`) : $t(`/团`) }}</text>
							<text class="y-money" v-if="item.ot_price">{{ $t(`￥`) }}{{ item.ot_price }}</text>
						</view>
						<view class="tag">{{ item.price_type == 1 ? $t(`按人计价`) : $t(`按团计价`) }}</view>
					</view>
				</view>
			</view>
		</view>
		<view class="noCommodity" v-if="projectList.length == 0 && !loading">
			<view class="emptyBox">
				<view class="tips">{{ $t(`暂无研学项目`) }}</view>
			</view>
		</view>
	</view>
</template>

<script>
import { studyProjectListApi } from "@/api/study.js";

export default {
	data() {
		return {
			projectList: [],
			page: 1,
			limit: 10,
			loading: false,
			loadend: false,
		};
	},
	onLoad() {
		this.getList();
	},
	onPullDownRefresh() {
		this.page = 1;
		this.loadend = false;
		this.getList();
		setTimeout(() => {
			uni.stopPullDownRefresh();
		}, 500);
	},
	onReachBottom() {
		if (!this.loadend) {
			this.page++;
			this.getList();
		}
	},
	methods: {
		getList() {
			let that = this;
			if (that.loading) return;
			that.loading = true;
			studyProjectListApi({ page: that.page, limit: that.limit })
				.then((res) => {
					let list = (res.data && res.data.list) || [];
					if (that.page == 1) {
						that.projectList = list;
					} else {
						that.projectList = that.projectList.concat(list);
					}
					if (list.length < that.limit) that.loadend = true;
					that.loading = false;
				})
				.catch((err) => {
					that.loading = false;
					that.$util.Tips({ title: err });
				});
		},
		goDetail(item) {
			uni.navigateTo({
				url: "/pages/study/project_detail/index?id=" + item.id,
			});
		},
	},
};
</script>

<style scoped lang="scss">
.project-list {
	min-height: 100vh;
	background-color: #f5f5f5;
}

.list {
	padding: 20rpx 24rpx;

	.item {
		background-color: #fff;
		border-radius: 16rpx;
		margin-bottom: 20rpx;
		padding: 24rpx;
	}

	.pictrue {
		width: 200rpx;
		height: 200rpx;
		border-radius: 12rpx;
		overflow: hidden;
		flex-shrink: 0;
		background-color: #f5f5f5;

		image {
			width: 100%;
			height: 100%;
		}
	}

	.text {
		flex: 1;
		margin-left: 20rpx;
		min-width: 0;

		.name {
			font-size: 30rpx;
			font-weight: bold;
			color: #282828;
		}

		.lecturer {
			font-size: 24rpx;
			color: #999;
		}

		.bottom {
			.money {
				font-size: 24rpx;
				color: #e93323;

				.num {
					font-size: 36rpx;
					font-weight: bold;
				}

				.unit {
					font-size: 22rpx;
					color: #999;
				}

				.y-money {
					font-size: 22rpx;
					color: #999;
					text-decoration: line-through;
					margin-left: 12rpx;
				}
			}

			.tag {
				padding: 4rpx 16rpx;
				border-radius: 20rpx;
				font-size: 22rpx;
				color: #e93323;
				background-color: rgba(233, 51, 35, 0.08);
			}
		}
	}
}

.noCommodity {
	.emptyBox {
		padding-top: 200rpx;
		text-align: center;

		.tips {
			color: #999;
			font-size: 26rpx;
		}
	}
}
</style>
