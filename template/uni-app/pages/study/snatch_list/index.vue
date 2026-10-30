<template>
	<view class="snatch-list">
		<view class="tab-bar">
			<view class="tab-item" :class="{ active: current === 0 }" @tap="switchTab(0)">{{ $t(`研学项目`) }}</view>
			<view class="tab-item" :class="{ active: current === 1 }" @tap="switchTab(1)">{{ $t(`研学抢购`) }}</view>
		</view>

		<!-- 研学项目列表 -->
		<view class="list" v-if="current === 0">
			<view class="project-item" v-for="(item, index) in projectList" :key="index" @tap="goProjectDetail(item)">
				<view class="pictrue">
					<image :src="item.image" mode="aspectFill" v-if="item.image"></image>
					<view class="cover-empty" v-else>{{ $t(`暂无封面`) }}</view>
				</view>
				<view class="text acea-row row-column-around">
					<view class="name line1">{{ item.title }}</view>
					<view class="lecturer line1" v-if="item.lecturer_name">{{ item.lecturer_name }}</view>
					<view class="money">
						{{ $t(`￥`) }}
						<text class="num">{{ item.price }}</text>
						<text class="unit">{{ item.price_type == 1 ? $t(`/人`) : $t(`/团`) }}</text>
					</view>
				</view>
				<view class="right">
					<view class="buy" v-if="item.product_id">{{ $t(`可购买`) }}</view>
					<view class="buy wait" v-else>{{ $t(`未开放`) }}</view>
				</view>
			</view>
		</view>
		<view class="noCommodity" v-if="current === 0 && projectList.length == 0 && !projectLoading">
			<view class="emptyBox">
				<view class="tips">{{ $t(`暂无研学项目`) }}</view>
			</view>
		</view>

		<!-- 研学抢购列表 -->
		<view class="list" v-if="current === 1">
			<view
				class="item acea-row"
				v-for="(item, index) in snatchList"
				:key="index"
				@tap="goDetails(item)"
			>
				<view class="pictrue">
					<image :src="item.image" mode="aspectFill"></image>
				</view>
				<view class="text acea-row row-column-around">
					<view class="name line1">{{ item.store_name }}</view>
					<view class="snatch-title line1">{{ item.title }}</view>
					<view class="money">
						{{ $t(`￥`) }}
						<text class="num">{{ item.price }}</text>
						<text class="y-money">{{ $t(`￥`) }}{{ item.ot_price }}</text>
					</view>
					<view class="quota">
						{{ $t(`剩余`) }}{{ item.quota }}/{{ item.quota_show }}{{ $t(`名额`) }}
					</view>
					<view class="progress">
						<view class="progress-bar" :style="'width:' + (item.percent || 0) + '%;'"></view>
					</view>
				</view>
				<view class="right acea-row row-column-around">
					<view class="grab" v-if="item.snatch_status === 1">{{ $t(`抢购中`) }}</view>
					<view class="grab wait" v-else-if="item.snatch_status === 2">{{ $t(`未开始`) }}</view>
					<view class="grab end" v-else-if="item.snatch_status === 3">{{ $t(`已售罄`) }}</view>
					<view class="grab end" v-else>{{ $t(`已结束`) }}</view>
					<countDown
						v-if="item.snatch_status === 1 || item.snatch_status === 2"
						:is-day="false"
						:tip-text="' '"
						:day-text="' '"
						:hour-text="':'"
						:minute-text="':'"
						:second-text="' '"
						:datatime="item.last_time"
					></countDown>
				</view>
			</view>
		</view>
		<view class="noCommodity" v-if="current === 1 && snatchList.length == 0 && !loading">
			<view class="emptyBox">
				<view class="tips">{{ $t(`暂无抢购场次，去看看别的吧`) }}</view>
			</view>
		</view>
	</view>
</template>

<script>
import { snatchIndexApi, studyProjectListApi } from "@/api/study.js";
import countDown from "@/components/countDown";
import { toLogin } from "@/libs/login.js";
import { mapGetters } from "vuex";

export default {
	components: {
		countDown,
	},
	computed: mapGetters(["isLogin"]),
	data() {
		return {
			current: 0,
			projectList: [],
			projectPage: 1,
			projectLimit: 10,
			projectLoading: false,
			projectLoadend: false,
			snatchList: [],
			page: 1,
			limit: 10,
			loading: false,
			loadend: false,
		};
	},
	onLoad() {
		this.getProjectList();
	},
	onShow() {
		// 从商品/项目详情返回后刷新当前 tab
		if (this.current === 0) {
			this.projectPage = 1;
			this.projectLoadend = false;
			this.getProjectList();
		} else {
			this.page = 1;
			this.loadend = false;
			this.getList();
		}
	},
	onPullDownRefresh() {
		if (this.current === 0) {
			this.projectPage = 1;
			this.projectLoadend = false;
			this.getProjectList();
		} else {
			this.page = 1;
			this.loadend = false;
			this.getList();
		}
		setTimeout(() => {
			uni.stopPullDownRefresh();
		}, 500);
	},
	onReachBottom() {
		if (this.current === 0) {
			if (!this.projectLoadend) {
				this.projectPage++;
				this.getProjectList();
			}
		} else {
			if (!this.loadend) {
				this.page++;
				this.getList();
			}
		}
	},
	methods: {
		switchTab(index) {
			if (this.current === index) return;
			this.current = index;
			if (index === 0 && !this.projectList.length) {
				this.projectPage = 1;
				this.getProjectList();
			}
			if (index === 1 && !this.snatchList.length) {
				this.page = 1;
				this.getList();
			}
		},
		getProjectList() {
			let that = this;
			if (that.projectLoading) return;
			that.projectLoading = true;
			studyProjectListApi({ page: that.projectPage, limit: that.projectLimit })
				.then((res) => {
					let data = res.data || {};
					let list = data.list || [];
					if (that.projectPage == 1) {
						that.projectList = list;
					} else {
						that.projectList = that.projectList.concat(list);
					}
					if (list.length < that.projectLimit) that.projectLoadend = true;
					that.projectLoading = false;
				})
				.catch((err) => {
					that.projectLoading = false;
					that.$util.Tips({ title: err });
				});
		},
		goProjectDetail(item) {
			uni.navigateTo({
				url: "/pages/study/project_detail/index?id=" + item.id,
			});
		},
		getList() {
			let that = this;
			if (that.loading) return;
			that.loading = true;
			snatchIndexApi({ page: that.page, limit: that.limit })
				.then((res) => {
					let list = res.data || [];
					if (that.page == 1) {
						that.snatchList = list;
					} else {
						that.snatchList = that.snatchList.concat(list);
					}
					if (list.length < that.limit) that.loadend = true;
					that.loading = false;
				})
				.catch((err) => {
					that.loading = false;
					that.$util.Tips({ title: err });
				});
		},
		goDetails(item) {
			if (this.isLogin === false) return toLogin();
			uni.navigateTo({
				url: "/pages/goods_details/index?id=" + item.product_id,
			});
		},
	},
};
</script>

<style scoped lang="scss">
.snatch-list {
	min-height: 100vh;
	background-color: #f5f5f5;
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
		padding: 16rpx 0;

		&.active {
			color: #e93323;
			font-weight: bold;
			border-bottom: 4rpx solid #e93323;
		}
	}
}

.list {
	padding: 20rpx 24rpx;

	.project-item,
	.item {
		background-color: #fff;
		border-radius: 16rpx;
		margin-bottom: 20rpx;
		padding: 24rpx;
	}

	.project-item {
		display: flex;
		align-items: center;
	}

	.pictrue {
		width: 200rpx;
		height: 200rpx;
		border-radius: 12rpx;
		overflow: hidden;
		flex-shrink: 0;

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
			font-size: 24rpx;
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

		.snatch-title {
			font-size: 24rpx;
			color: #999;
		}

		.money {
			font-size: 24rpx;
			color: #e93323;

			.num {
				font-size: 36rpx;
				font-weight: bold;
			}

			.unit {
				font-size: 24rpx;
				color: #999;
				margin-left: 8rpx;
			}

			.y-money {
				font-size: 24rpx;
				color: #999;
				text-decoration: line-through;
				margin-left: 12rpx;
			}
		}

		.quota {
			font-size: 24rpx;
			color: #999;
		}

		.progress {
			height: 12rpx;
			background-color: #f5f5f5;
			border-radius: 6rpx;
			overflow: hidden;

			.progress-bar {
				height: 100%;
				background-color: #e93323;
				border-radius: 6rpx;
			}
		}
	}

	.right {
		flex-shrink: 0;
		margin-left: 16rpx;
		align-items: flex-end;

		.grab {
			padding: 8rpx 24rpx;
			background-color: #e93323;
			color: #fff;
			font-size: 24rpx;
			border-radius: 24rpx;

			&.wait {
				background-color: #f7a72e;
			}

			&.end {
				background-color: #ccc;
			}
		}

		.buy {
			padding: 8rpx 24rpx;
			background-color: #e93323;
			color: #fff;
			font-size: 24rpx;
			border-radius: 24rpx;

			&.wait {
				background-color: #ccc;
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
